<?php
require __DIR__ . '/vendor/autoload.php';

use League\OAuth2\Client\Provider\Google;

// プロバイダー設定
$provider = new Google([
    'clientId'     => 'あなたのCLIENT_ID',
    'clientSecret' => 'あなたのCLIENT_SECRET',
    'redirectUri'  => 'http://localhost:8000/callback.php',
]);

// GoogleログインのURLにリダイレクト
$authUrl = $provider->getAuthorizationUrl();
$_SESSION['oauth2state'] = $provider->getState();

header('Location: ' . $authUrl);
exit;