# Cognito Token

A PHP library for verifying AWS Cognito JWT tokens (ID tokens and Access tokens).

## Overview

The `CognitoTokenVerifier` class provides functionality to verify JSON Web Tokens (JWTs) issued by AWS Cognito. It handles:

- Signature verification using JSON Web Keys (JWK)
- Token expiration validation
- Issuer validation
- Audience validation for ID tokens
- Client ID validation for access tokens

## Installation

```bash
composer require uqi/cognito-token
```

## Requirements

- PHP 7.4 or higher
- `web-token/jwt-signature` package
- `web-token/jwt-core` package

## Implementation Options

You have two options for implementing token verification in your application:

### Option 1: Use this library directly

The simplest approach is to use this library as-is, which provides a complete solution for Cognito token verification.

```php
use UQI\Cognito\Tokens\CognitoTokenVerifier;

$verifier = new CognitoTokenVerifier(
    'us-east-1',              // AWS region
    'us-east-1_example',    // Cognito User Pool ID
    'example'  // Cognito Client ID
);

$payload = $verifier->verifyIdToken($idToken);
```

### Option 2: Create your own implementation

If you need custom functionality or want to integrate with specific systems, you can create your own implementation based on the principles in this library:

1. Fetch the JWKS (JSON Web Key Set) from Cognito
2. Parse and verify the JWT signature using the appropriate JWK
3. Validate token claims (expiration, issuer, audience, etc.)

This approach gives you more control but requires deeper understanding of JWT verification.

## Basic Usage

```php
use UQI\Cognito\Tokens\CognitoTokenVerifier;
use UQI\Cognito\Tokens\Exception\CognitoTokenException;

// Initialize the verifier
$verifier = new CognitoTokenVerifier(
    'us-east-1',              // AWS region
    'us-east-1_aQRUYfYJQ',    // Cognito User Pool ID
    'example'  // Cognito Client ID
);


// Verify an ID token
try {
    $payload = $verifier->verifyIdToken($idToken);
    // $payload now contains the decoded token claims
    print_r($payload);
} catch (CognitoTokenException $e) {
    echo "Token verification failed: " . $e->getMessage();
}


// Verify an ID token
try {
    $payload = $verifier->verifyIdToken($idToken);
    // $payload now contains the decoded token claims
    print_r($payload);
} catch (CognitoTokenException $e) {
    echo "Token verification failed: " . $e->getMessage();
}

// Verify an id|access token
try {
    $payload = $verifier->verifyToken($token);
    // $payload now contains the decoded token claims
    print_r($payload);
} catch (CognitoTokenException $e) {
    echo "Token verification failed: " . $e->getMessage();
}
```

## Caching

The library supports caching of JWKS (JSON Web Key Sets) to improve performance. By default, it uses a no-cache implementation, but you can provide your own cache implementation:

```php
use UQI\Cognito\Tokens\CognitoTokenVerifier;
use YourNamespace\YourCacheImplementation;

// Create your cache implementation that implements CacheInterface
$cache = new YourCacheImplementation();

// Initialize the verifier with cache
$verifier = new CognitoTokenVerifier(
    'us-east-1',              // AWS region
    'us-east-1_aQRUYfYJQ',    // Cognito User Pool ID
    'example',  // Cognito Client ID
    $cache                    // Cache implementation
);
```

## Implementing a Cache Driver

Create a class that implements the `CacheInterface`:

```php
namespace YourNamespace;

use UQI\Cognito\Tokens\CacheInterface;

class YourCacheImplementation implements CacheInterface
{
    public function put(string $key, $value, int $minutes)
    {
        // Store the value in your cache system
    }

    public function get(string $key)
    {
        // Retrieve the value from your cache system
        // Return null if not found
    }

    public function has(string $key): bool
    {
        // Check if the key exists in your cache system
        return false;
    }
}
```

## Laravel Integration

For Laravel users, a ready-to-use cache implementation is included with the library:

```php

use Illuminate\Support\Facades\Cache;
use UQI\Cognito\Tokens\CacheInterface;

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
```

Usage with Laravel:

```php
use UQI\Cognito\Tokens\CognitoTokenVerifier;
use UQI\Cognito\Tokens\LaravelCache;

// Initialize the verifier with Laravel cache
$verifier = new CognitoTokenVerifier(
    config('cognito.region'),
    config('cognito.user_pool_id'),
    config('cognito.client_id'),
    new LaravelCache()
);
```

## Error Handling

The library throws `CognitoTokenException` with specific error codes:

| Error Code                    | Description                                 |
| ----------------------------- | ------------------------------------------- |
| JWKS_FETCH_FAILED             | Failed to fetch JWKS from the remote URL    |
| JWKS_INVALID_FORMAT           | Invalid JWKS format - 'keys' not found      |
| NO_KID_IN_TOKEN               | No 'kid' found in JWT header                |
| NO_JWK_FOR_KID                | No matching JWK found for the specified kid |
| SIGNATURE_VERIFICATION_FAILED | JWT signature verification failed           |
| TOKEN_PAYLOAD_DECODING_FAILED | Failed to decode JWT payload                |
| INVALID_TOKEN                 | Invalid token                               |
| INVALID_ISSUER                | Invalid issuer in token                     |
| TOKEN_EXPIRED                 | Token is expired                            |
| INVALID_AUDIENCE              | Invalid audience in ID token                |
| MISSING_SUBJECT               | Missing subject (sub) claim in ID token     |
| INVALID_CLIENT_ID_ACCESS      | Invalid client_id in access token           |

Example error handling:

```php
use UQI\Cognito\Tokens\CognitoTokenVerifier;
use UQI\Cognito\Tokens\Exception\CognitoTokenException;

try {
    $payload = $verifier->verifyIdToken($token);
    // Token is valid
} catch (CognitoTokenException $e) {
    switch ($e->getCode()) {
        case CognitoTokenException::TOKEN_EXPIRED:
            echo "The token has expired";
            break;
        case CognitoTokenException::INVALID_ISSUER:
            echo "Invalid token issuer";
            break;
        default:
            echo "Token verification failed: " . $e->getMessage();
    }
}
```

## Class Reference

### CognitoTokenVerifier

#### Constructor

```php
/**
 * Constructor.
 *
 * @param string $region     AWS region (e.g., "us-east-1").
 * @param string $userPoolId Cognito User Pool ID (e.g., "us-east-1_aQRUYfYJQ").
 * @param string $clientId   Cognito Client ID.
 * @param CacheInterface|null $cacheDriver (optional) Cache implementation.
 *
 * @throws CognitoTokenException if JWKS fetching or decoding fails.
 */
public function __construct(string $region, string $userPoolId, string $clientId, CacheInterface $cacheDriver = null)
```

#### Methods

```php
/**
 * Verifies the token's signature and basic claims.
 *
 * @param string $jwt The JWT string.
 * @return array Decoded token payload.
 * @return array|false Decoded token payload or false on failure.
 * @throws CognitoTokenException if verification fails.
 */
public function verifyToken(string $jwt): array

```

```php
/**
 * Verifies a Cognito ID token.
 *
 * @param string $jwt The ID token.
 * @return array Decoded token payload.
 * @throws CognitoTokenException if any validation fails.
 */
public function verifyIdToken(string $jwt): array
```

```php
/**
 * Verifies a Cognito access token.
 *
 * @param string $jwt The access token.
 * @return array Decoded token payload.
 * @throws CognitoTokenException if any validation fails.
 */
public function verifyAccessToken(string $jwt): array
```

### CacheInterface

```php
/**
 * Stores a value in the cache.
 *
 * @param string $key     The cache key.
 * @param mixed  $value   The value to store.
 * @param int    $minutes Cache duration in minutes.
 */
public function put(string $key, $value, int $minutes);

/**
 * Retrieves a value from the cache.
 *
 * @param string $key The cache key.
 * @return mixed The cached value or null if not found.
 */
public function get(string $key);

/**
 * Checks if a key exists in the cache.
 *
 * @param string $key The cache key.
 * @return bool True if the key exists, false otherwise.
 */
public function has(string $key): bool;
```

## License

MIT
