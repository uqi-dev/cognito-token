<?php

namespace UQI\Cognito\Tokens;

use UQI\Cognito\Tokens\CacheInterface;
use Illuminate\Support\Facades\Cache;

class LaravelCache implements CacheInterface
{
    public function put(string $key, $value, int $minutes)
    {
        Cache::put($key, $value, $minutes);
    }

    public function get(string $key)
    {
        return Cache::get($key);
    }

    public function has(string $key): bool
    {
        return Cache::has($key);
    }
}
