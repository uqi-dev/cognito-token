<?php

use UQI\Cognito\Tokens\CognitoTokenVerifier;
use UQI\Cognito\Tokens\Exception\CognitoTokenException;
use Dotenv\Dotenv;
use Symfony\Component\VarDumper\VarDumper;

require __DIR__ . '/vendor/autoload.php';

// Load the .env file from the current directory
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


$token = '';


$cognitoRegion = $_ENV['COGNITO_REGION'];
$cognitoUserPoolId = $_ENV['COGNITO_USER_POOL_ID'];
$cognitoClientId = $_ENV['COGNITO_CLIENT_ID'];

$verifier = new CognitoTokenVerifier($cognitoRegion, $cognitoUserPoolId, $cognitoClientId);

try {
    $payload = $verifier->verifyIdToken($token);
    VarDumper::dump($payload);
} catch (CognitoTokenException $err) {
    VarDumper::dump($err);
}
