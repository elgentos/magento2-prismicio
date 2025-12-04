<?php

namespace Elgentos\PrismicIO\Model\Api;

use \Prismic\Cache\CacheInterface;
use \Magento\Framework\App\CacheInterface as MagentoCache;

class CacheProxy implements CacheInterface
{

    const CACHE_TAGS = [
        'PRISMICIO_API'
    ];

    /**
     * @var MagentoCache
     */
    private $magentoCache;

    /**
     * Request-level in-memory cache to reduce cache lookups within single request
     *
     * @var array
     */
    private $requestCache = [];

    public function __construct(
        MagentoCache $magentoCache
    ) {
        $this->magentoCache = $magentoCache;
    }

    /*
      *
      * @param string $key the key of the cache entry
      * @return boolean true if the cache has a value for this key, otherwise false
      */
    public function has($key)
    {
        return $this->magentoCache->getFrontend()->test($key);
    }

    /**
     * Returns the value of a cache entry from its key
     *
     *
     * @param string $key the key of the cache entry
     * @return mixed the value of the entry, as it was passed to CacheInterface::set, null if not present in cache
     */
    public function get($key)
    {
        // Check request-level cache first
        if (isset($this->requestCache[$key])) {
            return $this->requestCache[$key];
        }

        if (! $this->has($key)) {
            return null;
        }

        $value = \json_decode($this->magentoCache->getFrontend()->load($key));

        // Store in request cache for future lookups
        $this->requestCache[$key] = $value;

        return $value;
    }

    /**
     * Stores a new cache entry
     *
     * @param string $key the key of the cache entry
     * @param mixed $value the value of the entry
     * @param int $ttl the time (in seconds) until this cache entry expires
     * @return void
     */
    public function set($key, $value, $ttl = 0)
    {
        // Update request cache
        $this->requestCache[$key] = $value;

        // Store in persistent cache
        $this->magentoCache->getFrontend()->save(\json_encode($value), $key, static::CACHE_TAGS, $ttl);
    }

    /**
     * Deletes a cache entry, from its key
     *
     * @param string $key the key of the cache entry
     * @return void
     */
    public function delete($key)
    {
        // Remove from request cache
        unset($this->requestCache[$key]);

        // Remove from persistent cache
        $this->magentoCache->getFrontend()->remove($key);
    }

    /**
     * Clears the whole cache
     *
     * @return void
     */
    public function clear()
    {
        // Clear request cache
        $this->requestCache = [];

        // Clear persistent cache
        $this->magentoCache->getFrontend()->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, static::CACHE_TAGS);
    }
}
