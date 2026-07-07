<?php
require_once __DIR__ . '/../../vendor/autoload.php';

session_start();

use Dotenv\Dotenv;

// .env ファイルの読み込み
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$apiKey = $_ENV['RESEND_API_KEY'];


// POST データの取得
$body  = $_POST['body']  ?? '';
// セッションに保存
$_SESSION['body']  = $body;

$subject = $_ENV['MAIL_SUBJECT'] ?? '';
$from = $_ENV['MAIL_FROM_ADDRESS'] ?? '';
$to = $_ENV['MAIL_TO_ADDRESS'] ?? '';

// Resend API クライアントの初期化
$resend = Resend::client($apiKey);

$resend->emails->send([
    'from' => $from,
    'to' => $to,
    'subject' => $subject,
    'html' => $body
]);

header('Location: index.php');
