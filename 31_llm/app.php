<?php
// Composer のオートローダーを読み込む
require_once __DIR__ . '/vendor/autoload.php';

// 環境変数を読み込むための Dotenv ライブラリを使用
use Dotenv\Dotenv;

// .env ファイルから環境変数を読み込む
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 既存コードが定数を参照しているため、環境変数から定数を定義する
define('OLLAMA_BASE_URL', $_ENV['OLLAMA_BASE_URL'] ?? '');
define('OLLAMA_MODEL', $_ENV['OLLAMA_MODEL'] ?? 'qwen3.5:4b');
define('OLLAMA_IMAGE_MODEL', $_ENV['OLLAMA_IMAGE_MODEL'] ?? 'llava:7b');
