<?php
declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Slice;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Csp\Api\CspAwareActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Store\Model\ScopeInterface;

class SliceSimulator implements HttpGetActionInterface, CspAwareActionInterface
{
    private const XML_PATH_ENABLED = 'prismicio/simulator/enabled';
    private const XML_PATH_SECRET = 'prismicio/simulator/secret';

    public function __construct(
        protected PageFactory $resultPageFactory,
        protected ScopeConfigInterface $scopeConfig,
        protected RequestInterface $request,
        protected ForwardFactory $resultForwardFactory
    ) {
    }

    public function execute()
    {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $configSecret = $this->scopeConfig->getValue(self::XML_PATH_SECRET, ScopeInterface::SCOPE_STORE);
        if ($configSecret) {
            $requestSecret = (string) $this->request->getParam('secret');
            if (!hash_equals((string) $configSecret, $requestSecret)) {
                return $this->resultForwardFactory->create()->forward('noroute');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    public function modifyCsp(array $appliedPolicies): array
    {
        $appliedPolicies[] = new \Magento\Csp\Model\Policy\FetchPolicy(
            'frame-ancestors',
            false,
            ['https://prismic.io', 'https://*.prismic.io'],
            ['https']
        );

        $appliedPolicies[] = new \Magento\Csp\Model\Policy\FetchPolicy(
            'script-src',
            false,
            [
                'https://cdn.jsdelivr.net/npm/@prismicio/*',
                'https://cdn.jsdelivr.net/npm/lz-string/*',
            ],
            ['https']
        );

        return $appliedPolicies;
    }
}
