<?php
require_once __DIR__ . '/../../app.php';
require_once __DIR__ . '/../lib/Lang.php';
require_once __DIR__ . '/../services/GeminiService.php';

// CORS設定: ワイルドカード(Access-Control-Allow-Origin: *)を使用
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$input = file_get_contents('php://input');
$posts = json_decode($input, true);

if (!is_array($posts)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid JSON data'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// origin, fromLang, toLangの値の検証
if (!isset($posts['origin']) || !isset($posts['fromLang']) || !isset($posts['toLang'])) {
    $data = [
        'status' => 'error',
        'message' => 'Invalid input data'
    ];
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// 翻訳
$gemini = new GeminiService();
$posts['translate'] = $gemini->translate($posts['origin'], $posts['fromLang'], $posts['toLang']);
$posts['status'] = $posts['translate'] === null ? 'error' : 'success';

// JSON形式でレスポンス
$json = json_encode($posts, JSON_UNESCAPED_UNICODE);
echo $json;
