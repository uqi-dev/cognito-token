<?php

namespace UQI\Cognito\Tokens;

class NoCache implements CacheInterface
{
    public function put(string $key, $value, int $minutes)
    {
        // No-op, no caching
    }

    public function get(string $key)
    {
        return null;  // Always return null as there's no cache
    }

    public function has(string $key): bool
    {
        return false;  // Always return false as no cache is available
    }
}
