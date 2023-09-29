<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Webhook;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Helper\GetStoreView;
use Elgentos\PrismicIO\Model\Api;
use Exception;
use Magento\CmsUrlRewrite\Model\CmsPageUrlRewriteGenerator;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewrite as UrlRewriteResource;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Psr\Log\LoggerInterface;
use stdClass;

class Url implements HttpPostActionInterface, CsrfAwareActionInterface
{
    private RequestInterface $request;

    private ConfigurationInterface $configuration;

    private StoreManagerInterface $storeManager;

    private Api $apiFactory;

    private ResultFactory $resultFactory;

    private UrlFinderInterface $urlFinder;

    private UrlRewriteFactory $urlRewriteFactory;

    private UrlPersistInterface $urlPersist;

    private UrlRewriteResource $urlRewriteResource;

    private LoggerInterface $logger;

    private GetStoreView $getStoreView;

    public function __construct(
        RequestInterface       $request,
        ConfigurationInterface $configuration,
        StoreManagerInterface  $storeManager,
        Api                    $apiFactory,
        ResultFactory          $resultFactory,
        UrlFinderInterface     $urlFinder,
        UrlRewriteFactory      $urlRewriteFactory,
        UrlPersistInterface    $urlPersist,
        UrlRewriteResource     $urlRewriteResource,
        LoggerInterface        $logger,
        GetStoreView           $getStoreView
    ) {
        $this->request = $request;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
        $this->apiFactory = $apiFactory;
        $this->resultFactory = $resultFactory;
        $this->logger = $logger;
        $this->urlFinder = $urlFinder;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlPersist = $urlPersist;
        $this->urlRewriteResource = $urlRewriteResource;
        $this->getStoreView = $getStoreView;
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
        $urlRewriteDocumentTypes = $this->configuration->getUrlRewriteContentTypes(
            $store
        );

        if (!$urlRewriteDocumentTypes) {
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
            if (!$document) {
                continue;
            }

            $currentStore = $this->getStoreView->getCurrentStoreView($document);

            if (!$currentStore) {
                continue;
            }

            $urlRewrite = $this->findUrlRewrite($document, $currentStore);

            if ($urlRewrite && $urlRewrite->getEntityType() === CmsPageUrlRewriteGenerator::ENTITY_TYPE) {
                $this->deleteUrlRewrite($document, $currentStore);
            }

            if (!$urlRewrite || $urlRewrite->getEntityType() === CmsPageUrlRewriteGenerator::ENTITY_TYPE) {
                $this->createUrlRewrite($document, $currentStore);
            }
        }

        return $result->setData([
            'success' => true
        ]);
    }

    protected function findUrlRewrite(stdClass $document, StoreInterface $store): ?UrlRewrite
    {
        return $this->urlFinder->findOneByData([
            UrlRewrite::REQUEST_PATH => $document->uid,
            UrlRewrite::STORE_ID => $store->getId()
        ]);
    }

    protected function deleteUrlRewrite(stdClass $document, StoreInterface $store): void
    {
        $this->urlPersist->deleteByData([
            UrlRewrite::REQUEST_PATH => $document->uid,
            UrlRewrite::STORE_ID => $store->getId()
        ]);
    }

    protected function createUrlRewrite(stdClass $document, StoreInterface $store): void
    {
        $urlRewrite = $this->urlRewriteFactory->create();

        $urlRewrite->setEntityType('custom');
        $urlRewrite->setRequestPath($document->uid);
        $urlRewrite->setTargetPath('prismicio/direct/page/type/' . $document->type . '/uid/' . $document->uid);
        $urlRewrite->setStoreId($store->getId());

        try {
            $this->urlRewriteResource->save($urlRewrite);
        } catch (Exception $exception) {
            $this->logger->error('Could not save url rewrite for published prismic page.');
        }
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
