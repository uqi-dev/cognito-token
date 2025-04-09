<?php

use PHPUnit\Framework\TestCase;
use UQI\Cognito\Tokens\CognitoTokenVerifier;
use UQI\Cognito\Tokens\Exception\CognitoTokenException;

class CognitoTokenVerifierTest extends TestCase
{
    private CognitoTokenVerifier $verifier;

    protected function setUp(): void
    {
        // Initialize the CognitoTokenVerifier without cache.
        $this->verifier = new CognitoTokenVerifier(
            'us-east-1', 
            'us-east-1_aQRUYfYJQ', 
            '6c8hocs2p4v53bf9ol5m0orlfj',
            null // No cache (passing null instead of a cache mock)
        );
    }

    public function testVerifyIdTokenValid(): void
    {
        // Mock the payload to simulate a valid ID token.
        $token = 'valid_id_token'; // Replace with a valid JWT token string
        $payload = [
            'iss' => 'https://cognito-idp.us-east-1.amazonaws.com/us-east-1_aQRUYfYJQ',
            'aud' => '6c8hocs2p4v53bf9ol5m0orlfj', // Must match client ID
            'sub' => '84e88468-90d1-705f-8c83-4ec080d25c47', // Must be present
            'exp' => time() + 3600 // Token expiration set to 1 hour ahead
        ];

        // No need to mock cache in this test, just simulate valid payload.
        // Run the verification
        $result = $this->verifier->verifyIdToken($token);

        $this->assertEquals($payload, $result);
    }

    public function testVerifyIdTokenInvalidAudience(): void
    {
        $this->expectException(CognitoTokenException::class);
        $this->expectExceptionMessage('Invalid audience in id_token.');

        $token = 'invalid_id_token'; // Replace with an actual token
        $payload = [
            'iss' => 'https://cognito-idp.us-east-1.amazonaws.com/us-east-1_aQRUYfYJQ',
            'aud' => 'incorrect_client_id', // Invalid client ID
            'sub' => '84e88468-90d1-705f-8c83-4ec080d25c47',
            'exp' => time() + 3600
        ];

        // Simulating a failed verification
        $this->verifier->verifyIdToken($token);
    }

    public function testVerifyAccessTokenValid(): void
    {
        // Mock the payload to simulate a valid Access token.
        $token = 'valid_access_token'; // Replace with a valid JWT token string
        $payload = [
            'iss' => 'https://cognito-idp.us-east-1.amazonaws.com/us-east-1_aQRUYfYJQ',
            'aud' => '6c8hocs2p4v53bf9ol5m0orlfj',
            'client_id' => '6c8hocs2p4v53bf9ol5m0orlfj', // Matching client ID
            'exp' => time() + 3600
        ];

        // No need to mock cache in this test, just simulate valid payload.
        // Run the verification
        $result = $this->verifier->verifyAccessToken($token);

        $this->assertEquals($payload, $result);
    }

    public function testVerifyAccessTokenInvalidClientId(): void
    {
        $this->expectException(CognitoTokenException::class);
        $this->expectExceptionMessage('Invalid access token: client_id mismatch.');

        $token = 'invalid_access_token'; // Replace with an actual token
        $payload = [
            'iss' => 'https://cognito-idp.us-east-1.amazonaws.com/us-east-1_aQRUYfYJQ',
            'aud' => '6c8hocs2p4v53bf9ol5m0orlfj',
            'client_id' => 'incorrect_client_id', // Invalid client ID
            'exp' => time() + 3600
        ];

        // Simulating a failed verification
        $this->verifier->verifyAccessToken($token);
    }
}
