<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Webhook;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Model\Api;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\PageCache\Model\Cache\Type;
use Magento\Store\Model\StoreManagerInterface;

class Cache implements HttpPostActionInterface, CsrfAwareActionInterface
{
    private RequestInterface $request;

    private ConfigurationInterface $configuration;

    private StoreManagerInterface $storeManager;

    private Api $apiFactory;

    private ResultFactory $resultFactory;

    private TypeListInterface $typeList;

    public function __construct(
        RequestInterface       $request,
        ConfigurationInterface $configuration,
        StoreManagerInterface  $storeManager,
        Api                    $apiFactory,
        ResultFactory          $resultFactory,
        TypeListInterface      $typeList
    ) {
        $this->request = $request;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
        $this->apiFactory = $apiFactory;
        $this->resultFactory = $resultFactory;
        $this->typeList = $typeList;
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

        $store = $this->storeManager->getStore();
        $cacheFlushDocumentTypes = $this->configuration->getCacheFlushContentTypes(
            $store
        );

        if (!$cacheFlushDocumentTypes) {
            return $result->setData([
                'success' => true
            ]);
        }

        $documentIds = $payload['documents'] ?? [];
        if (empty($documentIds)) {
            return $result->setData([
                'success' => true
            ]);
        }

        $api = $this->apiFactory->create();
        foreach ($documentIds as $documentId) {
            $document = $api->getByID($documentId);
            if (in_array($document->type, $cacheFlushDocumentTypes)) {
                $this->typeList->cleanType(Type::TYPE_IDENTIFIER);

                break;
            }
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

    private function protectRoute(array $payload): bool
    {
        $accessToken = $this->configuration->getWebhookSecret($this->storeManager->getStore());

        if ($payload['secret'] ?? '' === $accessToken) {
            return true;
        }

        return false;
    }
}
