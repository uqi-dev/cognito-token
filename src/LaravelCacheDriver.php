<?php

namespace UQI\Cognito\Tokens;

use Illuminate\Support\Facades\Cache;

use UQI\Cognito\Tokens\CacheInterface;

/**
 * 
 * Class LaravelCacheDriver
 *
 * This class acts as a bridge between the UQI Cognito Token library
 * and Laravel's caching system. It implements the CacheInterface
 * required by the Cognito token verifier, using Laravel's cache facade.
 *
 * The store (e.g., file, redis, memcached) is configurable via constructor,
 * allowing you to optimize for performance in production by using fast memory stores.
 */
class LaravelCacheDriver implements CacheInterface
{
    /**
     * The name of the Laravel cache store to use.
     *
     * @var string
     */
    protected $store;

    /**
     * Create a new cache driver instance.
     *
     * @param string $store Laravel cache store name (e.g., 'file', 'redis', 'memcached').
     */
    public function __construct(string $store = 'file')
    {
        $this->store = $store;
    }

    /**
     * Store a value in the cache for the given number of minutes.
     *
     * @param string $key Cache key.
     * @param mixed $value The value to cache.
     * @param int $minutes Duration in minutes to keep the item.
     */
    public function put(string $key, $value, int $minutes)
    {
        Cache::store($this->store)->put($key, $value, $minutes);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param string $key Cache key.
     * @return mixed|null The cached value, or null if not found.
     */
    public function get(string $key)
    {
        return Cache::store($this->store)->get($key);
    }

    /**
     * Determine if the given cache key exists.
     *
     * @param string $key Cache key.
     * @return bool True if the key exists in the cache, false otherwise.
     */
    public function has(string $key): bool
    {
        return Cache::store($this->store)->has($key);
    }
}
