<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Model\Api;

use Prismic\Cache\CacheInterface;
use Magento\Framework\App\CacheInterface as MagentoCache;
use Zend_Cache;

class CacheProxy implements CacheInterface
{
    private const CACHE_TAGS = [
        'PRISMICIO_API'
    ];

    /** @var MagentoCache */
    private $magentoCache;

    /**
     * Constructor.
     *
     * @param MagentoCache $magentoCache
     */
    public function __construct(
        MagentoCache $magentoCache
    ) {
        $this->magentoCache = $magentoCache;
    }

    /**
     * Check if there is caching for the given key
     *
     * @param string $key
     *
     * @return bool|int
     */
    public function has($key)
    {
        return $this->magentoCache->getFrontend()->test($key);
    }

    /**
     * Returns the value of a cache entry from its key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            return null;
        }

        return json_decode($this->magentoCache->getFrontend()->load($key));
    }

    /**
     * Stores a new cache entry
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $ttl
     *
     * @return void
     */
    public function set($key, $value, $ttl = 0)
    {
        $this->magentoCache->getFrontend()
            ->save(
                json_encode($value),
                $key,
                static::CACHE_TAGS,
                $ttl
            );
    }

    /**
     * Deletes a cache entry, from its key
     *
     * @param string $key
     *
     * @return void
     */
    public function delete($key)
    {
        $this->magentoCache->getFrontend()->remove($key);
    }

    /**
     * Clears the whole cache
     *
     * @return void
     */
    public function clear()
    {
        $this->magentoCache->getFrontend()->clean(
            Zend_Cache::CLEANING_MODE_MATCHING_TAG,
            static::CACHE_TAGS
        );
    }
}
