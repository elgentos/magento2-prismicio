<?php
declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Slice;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\ScopeInterface;

class Render implements HttpGetActionInterface, HttpPostActionInterface, CsrfAwareActionInterface
{
    private const XML_PATH_ENABLED = 'prismicio/simulator/enabled';
    private const XML_PATH_SECRET = 'prismicio/simulator/secret';
    private const XML_PATH_BLOCK_PREFIX = 'prismicio/simulator/block_prefix';
    private const XML_PATH_HANDLE_PREFIX = 'prismicio/simulator/handle_prefix';

    public function __construct(
        protected JsonFactory $resultJsonFactory,
        protected LayoutInterface $layout,
        protected HttpRequest $request,
        protected ScopeConfigInterface $scopeConfig,
        protected ForwardFactory $resultForwardFactory
    ) {
    }

    public function execute()
    {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        // POST+AJAX only (blocks direct browser navigation), despite implementing HttpGetActionInterface for routing.
        if (!$this->request->isAjax() || !$this->request->isPost()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $configSecret = $this->scopeConfig->getValue(self::XML_PATH_SECRET, ScopeInterface::SCOPE_STORE);
        if ($configSecret) {
            $requestSecret = (string) $this->request->getParam('secret');
            if (!hash_equals((string) $configSecret, $requestSecret)) {
                return $this->resultForwardFactory->create()->forward('noroute');
            }
        }

        $sliceDataJson = $this->request->getParam('slice');
        if (!$sliceDataJson) {
            return $this->resultJsonFactory->create()->setData(['error' => 'No slice data provided']);
        }

        $sliceData = json_decode($sliceDataJson);
        if (!$sliceData || !isset($sliceData->slice_type)) {
            return $this->resultJsonFactory->create()->setData(['error' => 'Invalid slice data: ' . $sliceDataJson]);
        }

        $sliceType = $sliceData->slice_type;
        $handle = $this->getLayoutHandle($sliceType);

        try {
            $this->layout->getUpdate()->addHandle($handle);
            $this->layout->getUpdate()->load();
            $this->layout->generateXml();
            $this->layout->generateElements();

            $blockNames = $this->getBlockNames($sliceType);

            $block = null;
            foreach ($blockNames as $name) {
                $block = $this->layout->getBlock($name);
                if ($block) {
                    $blockName = $name;
                    break;
                }
            }

            if (!$block) {
                return $this->resultJsonFactory->create()->setData([
                    'error' => 'Block not found for slice type: ' . $sliceType,
                    'tried_handles' => [$handle],
                    'tried_blocks' => $blockNames
                ]);
            }

            // Falls back to raw data for blocks that don't follow the elgentos setDocument() convention.
            if (method_exists($block, 'setDocument')) {
                $block->setDocument($sliceData);
            } else {
                $block->setData('document', $sliceData);
            }

            $html = $block->toHtml();
            $result = $this->resultJsonFactory->create();

            return $result->setData([
                'html' => $html,
                'block_name' => $blockName,
                'handle' => $handle
            ]);
        } catch (\Throwable $e) {
            $result = $this->resultJsonFactory->create();
            return $result->setData([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Candidate block names to try, in order; plugin this to support a different naming convention.
     *
     * @return string[]
     */
    public function getBlockNames(string $sliceType): array
    {
        $cleanSliceType = str_replace('-', '_', $sliceType);

        $names = [$sliceType];
        if ($cleanSliceType !== $sliceType) {
            $names[] = $cleanSliceType;
        }

        $prefix = trim((string) $this->scopeConfig->getValue(self::XML_PATH_BLOCK_PREFIX, ScopeInterface::SCOPE_STORE));
        if ($prefix !== '') {
            $names[] = $prefix . $sliceType;
            if ($cleanSliceType !== $sliceType) {
                $names[] = $prefix . $cleanSliceType;
            }
        }

        return array_values(array_unique($names));
    }

    /**
     * The layout handle to load for a given slice type; plugin this for something other than a fixed prefix.
     */
    public function getLayoutHandle(string $sliceType): string
    {
        $cleanSliceType = str_replace('-', '_', $sliceType);
        $prefix = trim((string) $this->scopeConfig->getValue(self::XML_PATH_HANDLE_PREFIX, ScopeInterface::SCOPE_STORE));

        return $prefix . $cleanSliceType;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
