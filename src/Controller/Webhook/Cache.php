<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Webhook;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Model\CacheTypes;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\PageCache\Model\Cache\Type;
use Magento\Store\Model\StoreManagerInterface;

class Cache implements HttpPostActionInterface, CsrfAwareActionInterface
{
    private RequestInterface $request;

    private ConfigurationInterface $configuration;

    private StoreManagerInterface $storeManager;

    private ResultFactory $resultFactory;

    private TypeListInterface $typeList;

    private StateInterface $cacheState;

    public function __construct(
        RequestInterface       $request,
        ConfigurationInterface $configuration,
        StoreManagerInterface  $storeManager,
        ResultFactory          $resultFactory,
        TypeListInterface      $typeList,
        StateInterface         $cacheState,
    ) {
        $this->request = $request;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
        $this->typeList = $typeList;
        $this->cacheState = $cacheState;
    }

    public function execute(): ?ResultInterface
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $payload = json_decode($this->request->getContent() ?? '', true);
        if (!$payload) {
            return $result->setData([
                'success' => true
            ]);
        }

        if (!$this->protectRoute($payload)) {
            return null;
        }

        $documentIds = $payload['documents'] ?? [];
        if (empty($documentIds)) {
            return $result->setData([
                'success' => true
            ]);
        }

        $this->typeList->cleanType(Type::TYPE_IDENTIFIER);

        if ($this->cacheState->isEnabled(CacheTypes::TYPE_DOCUMENTS)) {
            $this->typeList->cleanType(CacheTypes::TYPE_DOCUMENTS);
        }

        return $result->setData([
            'success' => true
        ]);
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @throws NoSuchEntityException
     */
    private function protectRoute(array $payload): bool
    {
        $accessToken = $this->configuration->getWebhookSecret($this->storeManager->getStore());

        if (($payload['secret'] ?? '') === $accessToken) {
            return true;
        }

        return false;
    }
}
