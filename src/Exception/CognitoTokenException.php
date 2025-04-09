<?php

namespace UQI\Cognito\Tokens\Exception;

use Exception;

class CognitoTokenException extends Exception
{
    // Define specific error code constants
    public const JWKS_FETCH_FAILED             = 1001;
    public const JWKS_INVALID_FORMAT           = 1002;
    public const NO_KID_IN_TOKEN               = 1003;
    public const NO_JWK_FOR_KID                = 1004;
    public const SIGNATURE_VERIFICATION_FAILED = 1005;
    public const TOKEN_PAYLOAD_DECODING_FAILED = 1006;
    public const INVALID_TOKEN                 = 1007;
    public const INVALID_ISSUER                = 1008;
    public const TOKEN_EXPIRED                 = 1009;
    public const INVALID_AUDIENCE              = 1010;
    public const MISSING_SUBJECT               = 1011;
    public const INVALID_CLIENT_ID_ACCESS      = 1012;

    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
