<?php
require '../vendor/autoload.php';

use Mpdf\Mpdf;

// 初期化（日本語モード・A4サイズ）
$config = [
    'mode' => 'ja-JP',
    'format' => 'A4'
];

$mpdf = new Mpdf($config);

// HTMLを書き込み
$html = '
    <h1 style="color: #4f46e5;">こんにちは、mPDF！</h1>
    <p>これは最小構成のサンプルです。</p>
';
$mpdf->WriteHTML($html);

// ブラウザに表示 (I: Inline, D: Download)
$mpdf->Output('test.pdf', 'I');
