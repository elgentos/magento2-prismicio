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
        if (! $this->has($key)) {
            return null;
        }

        return \json_decode($this->magentoCache->getFrontend()->load($key));
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
        $this->magentoCache->getFrontend()->remove($key);
    }

    /**
     * Clears the whole cache
     *
     * @return void
     */
    public function clear()
    {
        $this->magentoCache->getFrontend()->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, static::CACHE_TAGS);
    }
}
