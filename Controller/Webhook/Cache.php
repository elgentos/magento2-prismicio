<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Webhook;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Model\Api;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
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

    private CacheManager $cacheManager;

    public function __construct(
        RequestInterface       $request,
        ConfigurationInterface $configuration,
        StoreManagerInterface  $storeManager,
        Api                    $apiFactory,
        ResultFactory          $resultFactory,
        TypeListInterface      $typeList,
        CacheManager           $cacheManager
    ) {
        $this->request = $request;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
        $this->apiFactory = $apiFactory;
        $this->resultFactory = $resultFactory;
        $this->typeList = $typeList;
        $this->cacheManager = $cacheManager;
    }

    public function execute(): ?ResultInterface
    {
        if (!$this->protectRoute()) {
            return null;
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $payload = json_decode($this->request->getContent() ?? '', true);
        if (!$payload) {
            return $result->setData([
                'success' => true
            ]);
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
        $cacheTags = [];
        $shouldClearOverview = false;

        foreach ($documentIds as $documentId) {
            $document = $api->getByID($documentId);
            if (in_array($document->type, $cacheFlushDocumentTypes)) {
                // Clear document-specific cache
                $cacheTags[] = 'PRISMICIO_DOC_' . $documentId;

                // Check if this is an overview content type for selective invalidation
                $shouldClearOverview = true;
            }
        }

        // Clear related caches if we found matching documents
        if (!empty($cacheTags)) {
            // Clear document-specific caches
            $this->cacheManager->clean($cacheTags);

            // Clear API cache
            $this->cacheManager->clean(['PRISMICIO_API']);

            // Clear overview cache if document types match overview content
            if ($shouldClearOverview) {
                $this->cacheManager->clean(['PRISMICIO_OVERVIEW']);
            }

            // Also clear FPC as fallback to ensure all dependent caches are cleared
            $this->typeList->cleanType(Type::TYPE_IDENTIFIER);
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

    private function protectRoute()
    {
        $accessToken = $this->configuration->getWebhookSecret($this->storeManager->getStore());

        if ($this->request->getParam('secret') === $accessToken) {
            return true;
        }
    }
}
