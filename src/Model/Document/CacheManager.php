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
    private const CACHE_KEY_PATTERN = 'prismic_doc_%s_%s_%s';
    private const CACHE_TAG_ITEM_PATTERN = 'PRISMICIO_DOC_ITEM_%s_%s';

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
        string $lang
    ): mixed {
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

            return $unserialized;
        } catch (Exception $e) {

            return null;
        }
    }

    public function set(
        StdClass $document,
        string $type,
        string $uid,
        string $lang
    ): void {
        if (!$this->cacheState->isEnabled(CacheTypes::TYPE_DOCUMENTS)) {
            return;
        }

        try {
            $key = $this->buildKey(
                $type,
                $uid,
                $lang
            );

            $tags = $this->buildTags($type, $uid);
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
        ?string $uid = null
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
                // Invalidate all documents of a specific type
                $tags = [sprintf(CacheTypes::TAG_DOCUMENT_ITEM, $type, '*')];
                $this->cache->clean($tags);
                return;
            }

            // Invalidate specific document
            $tags = [$this->buildItemTag($type, $uid)];
            $this->cache->clean($tags);
        } catch (Exception) {
        }
    }

    private function buildKey(
        string $type,
        string $uid,
        string $lang
    ): string {
        return sprintf(self::CACHE_KEY_PATTERN, $type, $uid, $lang);
    }

    private function buildTags(string $type, string $uid): array
    {
        return [
            CacheTypes::TAG_DOCUMENTS,
            $this->buildItemTag($type, $uid),
        ];
    }

    private function buildItemTag(string $type, string $uid): string
    {
        return sprintf(self::CACHE_TAG_ITEM_PATTERN, $type, $uid);
    }
}
