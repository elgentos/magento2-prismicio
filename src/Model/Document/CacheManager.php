<?php
/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */
declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\Document;

use Elgentos\PrismicIO\Model\CacheTypes;
use Exception;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use stdClass;

class CacheManager
{
    private const CACHE_KEY_PATTERN = 'prismic_doc_store_%s_website_%s_%s_%s_%s';
    private const CACHE_TAG_ITEM_PATTERN = 'PRISMICIO_DOC_ITEM_store_%s_website_%s_%s_%s';

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly SerializerInterface $serializer,
        private readonly StateInterface $cacheState,
        private readonly array $defaultConfig = []
    ) {
    }

    public function get(
        string $type,
        string $uid,
        string $lang,
        int $storeId,
        int $websiteId
    ): mixed {
        if (!$this->cacheState->isEnabled(CacheTypes::TYPE_DOCUMENTS)) {
            return null;
        }

        try {
            $key = $this->buildKey($type, $uid, $lang, $storeId, $websiteId);
            $cached = $this->cache->load($key);

            if ($cached === false) {
                return null;
            }

            $unserialized = $this->serializer->unserialize($cached);

            // Convert array back to stdClass if needed (from JSON serialization)
            if (is_array($unserialized)) {
                $unserialized = json_decode(json_encode($unserialized));
            }

            return $unserialized;
        } catch (Exception $e) {

            return null;
        }
    }

    public function set(
        StdClass $document,
        string $type,
        string $uid,
        string $lang,
        int $storeId,
        int $websiteId
    ): void {
        if (!$this->cacheState->isEnabled(CacheTypes::TYPE_DOCUMENTS)) {
            return;
        }

        try {
            $key = $this->buildKey(
                $type,
                $uid,
                $lang,
                $storeId,
                $websiteId
            );

            $tags = $this->buildTags($type, $uid, $storeId, $websiteId);
            $ttl = (int)($this->defaultConfig['ttl'] ?? 86400); // Default 1 day

            /** @var array|bool|float|int|null|string $document */
            $serialized = $this->serializer->serialize($document);
            $this->cache->save(
                $serialized,
                $key,
                $tags,
                $ttl
            );
        } catch (Exception) {
        }
    }

    public function invalidate(
        ?string $type = null,
        ?string $uid = null,
        ?int $storeId = null,
        ?int $websiteId = null
    ): void {
        if (!$this->cacheState->isEnabled(CacheTypes::TYPE_DOCUMENTS)) {
            return;
        }

        try {
            if ($type === null) {
                // Invalidate all Prismic documents
                $this->cache->clean([CacheTypes::TAG_DOCUMENTS]);
                return;
            }

            if ($uid === null) {
                // Invalidate all documents of a specific type (optionally for specific store/website)
                if ($storeId !== null && $websiteId !== null) {
                    // Invalidate specific store/website combination
                    $tags = [sprintf(CacheTypes::TAG_DOCUMENT_ITEM, $storeId, $websiteId, $type, '*')];
                } else {
                    // Invalidate all stores/websites for this type
                    $tags = [sprintf(CacheTypes::TAG_DOCUMENT_ITEM, '*', '*', $type, '*')];
                }
                $this->cache->clean($tags);
                return;
            }

            // Invalidate specific document
            if ($storeId !== null && $websiteId !== null) {
                // Invalidate specific document in specific store/website
                $tags = [$this->buildItemTag($type, $uid, $storeId, $websiteId)];
            } else {
                // Invalidate specific document across all stores/websites
                $tags = [sprintf(CacheTypes::TAG_DOCUMENT_ITEM, '*', '*', $type, $uid)];
            }
            $this->cache->clean($tags);
        } catch (Exception) {
        }
    }

    private function buildKey(
        string $type,
        string $uid,
        string $lang,
        int $storeId,
        int $websiteId
    ): string {
        return sprintf(self::CACHE_KEY_PATTERN, $storeId, $websiteId, $type, $uid, $lang);
    }

    private function buildTags(string $type, string $uid, int $storeId, int $websiteId): array
    {
        return [
            CacheTypes::TAG_DOCUMENTS,
            $this->buildItemTag($type, $uid, $storeId, $websiteId),
        ];
    }

    private function buildItemTag(string $type, string $uid, int $storeId, int $websiteId): string
    {
        return sprintf(self::CACHE_TAG_ITEM_PATTERN, $storeId, $websiteId, $type, $uid);
    }
}
