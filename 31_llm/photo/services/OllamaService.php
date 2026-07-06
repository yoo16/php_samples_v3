<?php

class OllamaService
{
    // ローカルで起動しているOllamaのベースURL
    private array $options = [
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n",
            'ignore_errors' => true,
            'content'       => '',
            'timeout'       => 60 // 画像解析は少し時間がかかる場合があるためタイムアウトを長めに設定
        ]
    ];

    /**
     * Ollamaに画像を送信し、解析結果を取得するメソッド
     * @param string $image_path 画像ファイルのパス
     * @return array 解析結果
     */
    public function image(string $image_path): array
    {
        if (!file_exists($image_path)) {
            return ['error' => '画像ファイルが見つかりません'];
        }

        $results = [];
        $model = OLLAMA_IMAGE_MODEL;

        $image = file_get_contents($image_path);
        if ($image === false) {
            return ['error' => '画像ファイルの読み込みに失敗しました'];
        }

        // Ollamaは単純なBase64文字列の配列を受け取る（MIMEタイプの付与は不要）
        $image_base64 = base64_encode($image);
        $prompt = 'この画像に写っている内容を日本語で説明してください。';

        // OllamaのAPIリクエストデータを作成
        $data = [
            'model'  => $model,
            'prompt' => $prompt,
            'images' => [$image_base64],
            'stream' => false // レスポンスを一括で受け取る
        ];

        // リクエストデータ、ヘッダーを設定
        $this->options['http']['content'] = json_encode($data, JSON_UNESCAPED_UNICODE);

        // ストリームコンテキストを作成
        $context = stream_context_create($this->options);

        $url = OLLAMA_BASE_URL . '/api/generate';

        // Ollama APIにリクエストを送信し、レスポンスを取得
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            $results['error'] = 'Ollama APIへのリクエストに失敗しました';
        } else {
            $json = json_decode($response, true);

            if (isset($json['response'])) {
                // ご提示のコードに合わせ、改行の変換とHTMLエスケープを処理
                $results['text'] = nl2br(htmlspecialchars($json['response']));
            } else {
                $results['error'] = '画像解析に失敗、または適切なレスポンスが得られませんでした';
            }
        }

        return $results;
    }
}
