<?php

namespace App;

use Mpdf\Mpdf;

class PdfGenerator
{
    private $config;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function generate($data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = $value ?? '';
        }

        // 1. パーツの準備
        // CSS
        $css = file_get_contents($this->config['css']);
        // Content
        $content = $this->render($this->config['template'], $data);
        // Layout
        $html = $this->render($this->config['layout'], ['content' => $content]);
        $html = trim($html);

        // 2. mPDFの初期化
        $mpdf = new Mpdf($this->config['mpdf']);
        // 強制設定：はみ出しによる自動改ページを無効化
        $mpdf->autoPageBreak = false;

        // 3. CSSとHTMLを分離して書き込む
        $mpdf->WriteHTML($css, 1);
        $mpdf->WriteHTML($html, 2);

        // 4. 出力直前にバッファの中身（警告文など）を完全に捨てる
        if (ob_get_length()) ob_end_clean();

        // 5. PDFを出力
        $fileName = $this->config['default_filename'];
        $mpdf->Output($fileName, 'D');
        exit;
    }

    private function render($path, $data)
    {
        extract($data);
        ob_start();
        include $path;
        return ob_get_clean() ?: '';
    }
}
