<?php
require_once __DIR__ . '/../vendor/autoload.php';
// 1. 設定ファイルの読み込み
require_once __DIR__ . '/config.php';

use \Mpdf\Mpdf;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// 2. データの準備
$data = $_POST;

if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['image']['tmp_name'];
    $data['image'] = $tmp_name;
}

// 3. レンダリング関数
function render($path, $data)
{
    extract($data);
    ob_start();
    include $path;
    return ob_get_clean();
}

// 4. PDF出力
$mpdf = new Mpdf($config['mpdf']);
// CSS読み込み
$css = file_get_contents($config['css']);
$mpdf->WriteHTML($css, 1);

// テンプレート読み込み
$html = render($config['template'], $data);
// HTMLをPDFに変換
$mpdf->WriteHTML($html);

// モード
$outputMode = ($data['mode'] === 'download') ? 'D' : 'I';
// PDF出力
$mpdf->Output('flyer.pdf', $outputMode);
