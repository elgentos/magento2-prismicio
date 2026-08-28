<?php

declare(strict_types=1);

namespace Elgentos\PrismicIO\Controller\Webhook;

use Elgentos\PrismicIO\Api\ConfigurationInterface;
use Elgentos\PrismicIO\Model\Api;
use Elgentos\PrismicIO\Model\CacheTypes;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Cache\CacheConstants;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\PageCache\Model\Cache\Type;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Cache implements HttpPostActionInterface, CsrfAwareActionInterface
{
    private RequestInterface $request;

    private ConfigurationInterface $configuration;

    private StoreManagerInterface $storeManager;

    private ResultFactory $resultFactory;

    private TypeListInterface $typeList;

    private StateInterface $cacheState;

    private Api $apiFactory;

    private CacheInterface $cache;

    private Type $fullPageCache;

    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        RequestInterface       $request,
        ConfigurationInterface $configuration,
        StoreManagerInterface  $storeManager,
        ResultFactory          $resultFactory,
        TypeListInterface      $typeList,
        StateInterface         $cacheState,
        Api                    $apiFactory,
        CacheInterface         $cache,
        Type                   $fullPageCache,
        ScopeConfigInterface   $scopeConfig,
    ) {
        $this->request = $request;
        $this->configuration = $configuration;
        $this->storeManager = $storeManager;
        $this->resultFactory = $resultFactory;
        $this->typeList = $typeList;
        $this->cacheState = $cacheState;
        $this->apiFactory = $apiFactory;
        $this->cache = $cache;
        $this->fullPageCache = $fullPageCache;
        $this->scopeConfig = $scopeConfig;
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

        if ($this->cacheState->isEnabled(CacheTypes::TYPE_DOCUMENTS)) {
            // Also clears Api::hasTag()'s cached tag list, tagged as part of this same type.
            $this->typeList->cleanType(CacheTypes::TYPE_DOCUMENTS);
        }

        if ($this->isBlockCacheEnabled()) {
            $this->purgeCachesForDocuments($documentIds);
        } else {
            $this->typeList->cleanType(Type::TYPE_IDENTIFIER);
        }

        return $result->setData([
            'success' => true
        ]);
    }

    private function isBlockCacheEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('prismicio/block_cache/enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Purges block_html and full_page separately - both get the same identity tags, but are
     * different cache stores. Only reaches content rendered through an IdentityInterface block
     * (Block\TemplateCacheable, StaticBlock); plain Template content was never tagged, hence
     * this being opt-in rather than replacing the type-wide flush below.
     */
    private function purgeCachesForDocuments(array $documentIds): void
    {
        $identities = [];
        foreach ($documentIds as $documentId) {
            $document = $this->apiFactory->getDocumentById($documentId);
            if (!$document || empty($document->type)) {
                continue;
            }

            $uid = $document->uid ?? $document->id ?? '';
            // Must match Block\DocumentCacheableTrait::getIdentities().
            $identities[] = 'prismicio_document_' . $document->type . '_' . $uid;
            foreach ($document->tags ?? [] as $tag) {
                $identities[] = 'prismicio_tag_' . $document->type . '_' . $tag;
            }
        }

        if (empty($identities)) {
            return;
        }

        $this->cache->clean($identities);
        $this->fullPageCache->clean(CacheConstants::CLEANING_MODE_MATCHING_ANY_TAG, $identities);
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
