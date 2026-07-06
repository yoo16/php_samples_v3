<?php
// Composer のオートローダーを読み込む
require_once __DIR__ . '/vendor/autoload.php';

// 環境変数を読み込むための Dotenv ライブラリを使用
use Dotenv\Dotenv;

// .env ファイルから環境変数を読み込む
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 既存コードが定数を参照しているため、環境変数から定数を定義する
define('GEMINI_API_KEY', $_ENV['GEMINI_API_KEY'] ?? '');
define('GEMINI_MODEL', $_ENV['GEMINI_MODEL'] ?? 'gemini-2.5-flash');
