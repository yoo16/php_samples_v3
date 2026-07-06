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
     * GeminiAPIにリクエストを送信し、レスポンスを取得するメソッド
     * @param string $prompt リクエストのプロンプト
     * @return string|null レスポンスのテキストデータ
     */
    function chat(string $prompt): ?string
    {
        if (empty($prompt)) return "プロンプトが空です";
        $url = sprintf(
            '%s%s:generateContent?key=%s',
            $this->baseURL,
            GEMINI_MODEL,
            GEMINI_API_KEY
        );

        // リクエストボディ作成
        $requestData = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
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

        // レスポンスをデコード
        $json = json_decode($response, true);
        // テキストデータを返す
        return $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

}