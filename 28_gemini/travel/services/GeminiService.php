<?php

class GeminiService
{
    // APIのベースURL
    private string $baseURL = 'https://generativelanguage.googleapis.com/v1beta/models/';
    private array $options = [
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n",
            'ignore_errors' => true,
            'content'       => ''
        ]
    ];

    /*
     * GeminiAPIにプロンプトを送信し、JSON形式のレスポンスを取得するメソッド
     * responseMimeType に application/json を指定して、必ずJSONで返させる
     * @param string $prompt リクエストのプロンプト
     * @return array|null デコード済みのJSONデータ（失敗時は null）
     */
    function generateJson(string $prompt): ?array
    {
        if (empty($prompt)) return null;

        $url = sprintf(
            '%s%s:generateContent?key=%s',
            $this->baseURL,
            GEMINI_MODEL,
            GEMINI_API_KEY
        );

        // リクエストボディ作成（JSONモードを指定）
        $requestData = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json'
            ]
        ];

        // リクエストヘッダーを設定
        $this->options['http']['content'] = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        // ストリームコンテキストを作成
        $context = stream_context_create($this->options);
        // GeminiAPIにリクエストを送信し、レスポンスを取得
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            return null;
        }

        // レスポンスをデコードし、テキスト部分（JSON文字列）を取り出す
        $json = json_decode($response, true);
        $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if ($text === null) {
            return null;
        }

        // JSON文字列を配列に変換して返す
        return json_decode($text, true);
    }
}
