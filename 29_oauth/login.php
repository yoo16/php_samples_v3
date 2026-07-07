<?php
require __DIR__ . '/../vendor/autoload.php';

session_start();

use League\OAuth2\Client\Provider\Google;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$clitentId = $_ENV['GOOGLE_CLIENT_ID'];
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
$callbackUrl = $_ENV['CALLBACK_URL'];

// プロバイダー設定
$provider = new Google([
    'clientId'     => $clitentId,
    'clientSecret' => $clientSecret,
    'redirectUri'  => $callbackUrl,
]);

// GoogleログインのURLにリダイレクト
$authUrl = $provider->getAuthorizationUrl();
$_SESSION['oauth2state'] = $provider->getState();

header('Location: ' . $authUrl);
exit;