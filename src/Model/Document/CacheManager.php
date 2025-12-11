<?php

namespace Elgentos\PrismicIO\Model\Document;

use Elgentos\PrismicIO\Logger\ApiLogger;
use Elgentos\PrismicIO\Model\CacheTypes;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;

class CacheManager
{
    private const CACHE_KEY_PATTERN = 'prismic_doc_%s_%s_%s';
    private const CACHE_TAG_ITEM_PATTERN = 'PRISMICIO_DOC_ITEM_%s_%s';

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ApiLogger
     */
    private $logger;

    /**
     * @var StateInterface
     */
    private $cacheState;

    /**
     * @var array
     */
    private $defaultConfig;

    /**
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     * @param StoreManagerInterface $storeManager
     * @param ApiLogger $logger
     * @param StateInterface $cacheState
     * @param array $defaultConfig Configuration from system.xml (injected)
     */
    public function __construct(
        CacheInterface $cache,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        ApiLogger $logger,
        StateInterface $cacheState,
        array $defaultConfig = []
    ) {
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->cacheState = $cacheState;
        $this->defaultConfig = $defaultConfig;
    }

    /**
     * Get cached document
     *
     * @param string $type Document type
     * @param string $uid Document UID
     * @param string $lang Language code
     * @return mixed|null
     */
    public function get(string $type, string $uid, string $lang)
    {
        if (!$this->cacheState->isEnabled(CacheTypes::TYPE_DOCUMENTS)) {
            return null;
        }

        try {
            $key = $this->buildKey($type, $uid, $lang);
            $cached = $this->cache->load($key);

            if ($cached === false) {
                return null;
            }

            $unserialized = $this->serializer->unserialize($cached);

            // Convert array back to stdClass if needed (from JSON serialization)
            if (is_array($unserialized)) {
                $unserialized = json_decode(json_encode($unserialized));
            }

            $this->logger->info(
                sprintf('Document cache hit: %s', $key)
            );

            return $unserialized;
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf('Error retrieving cached document: %s', $e->getMessage())
            );
            return null;
        }
    }

    /**
     * Set cached document
     *
     * @param mixed $document Document to cache
     * @param string $type Document type
     * @param string $uid Document UID
     * @param string $lang Language code
     * @return void
     */
    public function set($document, string $type, string $uid, string $lang): void
    {
        if (!$this->cacheState->isEnabled(CacheTypes::TYPE_DOCUMENTS)) {
            return;
        }

        try {
            $key = $this->buildKey($type, $uid, $lang);
            $tags = $this->buildTags($type, $uid);
            $ttl = (int)($this->defaultConfig['ttl'] ?? 86400); // Default 1 day

            $serialized = $this->serializer->serialize($document);
            $this->cache->save(
                $serialized,
                $key,
                $tags,
                $ttl
            );

            $this->logger->info(
                sprintf('Document cached: %s (TTL: %d seconds)', $key, $ttl)
            );
        } catch (\Exception $e) {
            $this->logger->info(
                sprintf('Error caching document: %s', $e->getMessage())
            );
        }
    }

    /**
     * Invalidate cached document(s)
     *
     * @param string|null $type Document type (null = invalidate all)
     * @param string|null $uid Document UID (ignored if type is null)
     * @return void
     */
    public function invalidate(?string $type = null, ?string $uid = null): void
    {
        if (!$this->cacheState->isEnabled(CacheTypes::TYPE_DOCUMENTS)) {
            return;
        }

        try {
            if ($type === null) {
                // Invalidate all Prismic documents
                $this->cache->clean([CacheTypes::TAG_DOCUMENTS]);
                $this->logger->info('All Prismic documents cache cleared');
                return;
            }

            if ($uid === null) {
                // Invalidate all documents of a specific type
                $tags = [sprintf(CacheTypes::TAG_DOCUMENT_ITEM, $type, '*')];
                $this->cache->clean($tags);
                $this->logger->info(
                    sprintf('All documents of type "%s" cache cleared', $type)
                );
                return;
            }

            // Invalidate specific document
            $tags = [$this->buildItemTag($type, $uid)];
            $this->cache->clean($tags);
            $this->logger->info(
                sprintf('Document cache cleared: %s_%s', $type, $uid)
            );
        } catch (\Exception $e) {
            $this->logger->info(
                sprintf('Error invalidating document cache: %s', $e->getMessage())
            );
        }
    }

    /**
     * Build cache key from document parameters
     *
     * @param string $type
     * @param string $uid
     * @param string $lang
     * @return string
     */
    private function buildKey(string $type, string $uid, string $lang): string
    {
        return sprintf(self::CACHE_KEY_PATTERN, $type, $uid, $lang);
    }

    /**
     * Build cache tags for document
     *
     * @param string $type
     * @param string $uid
     * @return array
     */
    private function buildTags(string $type, string $uid): array
    {
        return [
            CacheTypes::TAG_DOCUMENTS,
            $this->buildItemTag($type, $uid),
        ];
    }

    /**
     * Build individual document item tag
     *
     * @param string $type
     * @param string $uid
     * @return string
     */
    private function buildItemTag(string $type, string $uid): string
    {
        return sprintf(self::CACHE_TAG_ITEM_PATTERN, $type, $uid);
    }
}
