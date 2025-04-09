<?php

namespace UQI\Cognito\Tokens;

interface CacheInterface
{
    public function put(string $key, $value, int $minutes);
    public function get(string $key);
    public function has(string $key): bool;
}
