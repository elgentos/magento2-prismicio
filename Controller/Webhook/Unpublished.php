<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Webhook;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Model\Api;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use stdClass;

class Unpublished implements HttpPostActionInterface, CsrfAwareActionInterface
{
    private RequestInterface $request;

    private ResultFactory $resultFactory;

    private UrlPersistInterface $urlPersist;

    private Api $apiFactory;

    private ConfigurationInterface $configuration;

    private StoreManagerInterface $storeManager;

    public function __construct(
        RequestInterface $request,
        ResultFactory $resultFactory,
        UrlPersistInterface $urlPersist,
        ConfigurationInterface $configuration,
        StoreManagerInterface $storeManager,
        Api $apiFactory
    ) {

        $this->request       = $request;
        $this->resultFactory = $resultFactory;
        $this->urlPersist    = $urlPersist;
        $this->apiFactory    = $apiFactory;
        $this->configuration = $configuration;
        $this->storeManager  = $storeManager;
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

        $store = $this->storeManager->getStore();
        $api   = $this->apiFactory->create();
        foreach ($documentIds as $documentId) {
            $document = $api->getByID($documentId);
            if (!$document) {
                continue;
            }

            $this->deleteUrlRewrite($document, $store);
        }

        return $result->setData([
            'success' => true
        ]);
    }

    protected function deleteUrlRewrite(
        stdClass $document,
        StoreInterface $store
    ): void {
        $this->urlPersist->deleteByData([
            UrlRewrite::REQUEST_PATH => $document->uid,
            UrlRewrite::STORE_ID => $store->getId()
        ]);
    }

    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
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
