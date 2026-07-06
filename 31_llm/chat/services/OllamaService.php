<?php

class OllamaService
{
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
     * Ollamaにメッセージを送信し、応答を取得するメソッド
     * @param string $prompt プロンプト
     * @return array 応答結果
     */
    public function chat(string $prompt): array
    {
        $model = OLLAMA_MODEL;

        $data = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'stream' => false,
        ];

        $this->options['http']['header'] =
            "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n";

        $this->options['http']['content'] =
            json_encode($data, JSON_UNESCAPED_UNICODE);

        $context = stream_context_create($this->options);

        $url = OLLAMA_BASE_URL . '/api/chat';
        $response = file_get_contents(
            $url,
            false,
            $context
        );

        if ($response === false) {
            return ['error' => 'Ollama APIへの接続に失敗しました'];
        }

        $json = json_decode($response, true);

        if (!isset($json['message']['content'])) {
            return ['error' => '応答形式が不正です'];
        }

        return [
            'text' => $json['message']['content']
        ];
    }
}
