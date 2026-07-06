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

    /*
     * GeminiAPIに画像を送信し、解析結果を取得するメソッド
     * @param string $image_path 画像ファイルのパス
     * @return array 解析結果
     */
    function image(string $image_path): array
    {
        if (!file_exists($image_path)) {
            return ['error' => '画像ファイルが見つかりません'];
        }

        $results = [];
        $url = sprintf(
            '%s%s:generateContent?key=%s',
            $this->baseURL,
            GEMINI_MODEL,
            GEMINI_API_KEY
        );

        $image = file_get_contents($image_path);
        if ($image === false) {
            return ['error' => '画像ファイルの読み込みに失敗しました'];
        }

        $mime_type = mime_content_type($image_path) ?: 'image/jpeg';
        $image_base64 = base64_encode($image);
        $prompt = 'この画像に写っている内容を日本語で説明してください。';

        // リクエストデータを作成
        $data = [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    [
                        'inline_data' => [
                            'mime_type' => $mime_type,
                            'data' => $image_base64
                        ]
                    ]
                ]
            ]]
        ];

        // リクエストヘッダーを設定
        $this->options['http']['content'] = json_encode($data, JSON_UNESCAPED_UNICODE);

        // ストリームコンテキストを作成
        $context = stream_context_create($this->options);
        // GeminiAPIにリクエストを送信し、レスポンスを取得
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            $results['error'] = 'APIリクエストに失敗';
        } else {
            $json = json_decode($response, true);
            if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                $results['text'] = nl2br(htmlspecialchars($json['candidates'][0]['content']['parts'][0]['text']));
            } else {
                $results['error'] = '画像解析失敗';
            }
        }
        return $results;
    }

    /*
     * GeminiAPIを使用して翻訳を行うメソッド
     * @param string $origin 翻訳元のテキスト
     * @param string $fromLang 翻訳元の言語コード
     * @param string $toLang 翻訳先の言語コード
     * @return string|null 翻訳結果
     */
    function translate(string $origin, string $fromLang, string $toLang): ?string
    {
        if (empty($origin)) return "翻訳元のテキストが空です";
        if (empty($fromLang) || empty($toLang)) return "翻訳元または翻訳先の言語コードが空です";

        $url = sprintf(
            '%s%s:generateContent?key=%s',
            $this->baseURL,
            GEMINI_MODEL,
            GEMINI_API_KEY
        );

        $fromLang = Lang::getByCode($fromLang);
        $toLang = Lang::getByCode($toLang);

        $prompt = "Please translate from {$fromLang} to {$toLang} 
        without bracket character.
        If it cannot be translated, 
        please return it as it cannot be translated in {$toLang}.
        \n {$origin}";

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        // リクエストヘッダーを設定
        $this->options['http']['content'] = json_encode($data, JSON_UNESCAPED_UNICODE);

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
