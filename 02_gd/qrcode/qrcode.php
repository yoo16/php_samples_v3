<?php
require '../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

// QRコードのサイズ
$size = 300;
// QRコードのマージン
$margin = 10;

// URLを取得
$text = $_GET['url'] ?? '';
if (!$text) {
    http_response_code(400);
    echo 'URLが指定されていません。';
    exit;
}

// QRコード生成
$qrCode = new QrCode(
    data: $text,
    encoding: new Encoding('UTF-8'),
    size: $size,
    margin: $margin,
);

// PNGとして出力
$writer = new PngWriter();
$result = $writer->write($qrCode);

// 画像出力
header('Content-Type: ' . $result->getMimeType());
echo $result->getString();