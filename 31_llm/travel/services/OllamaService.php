<?php

class OllamaService
{
    private array $options = [
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\n",
            'ignore_errors' => true,
            'content'       => '',
            'timeout'       => 60
        ]
    ];

    public function chat(string $prompt): array
    {
        $data = [
            'model' => OLLAMA_MODEL,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'stream' => false,
            'format' => 'json',
        ];

        $this->options['http']['header'] =
            "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n";

        $this->options['http']['content'] =
            json_encode($data, JSON_UNESCAPED_UNICODE);

        $context = stream_context_create($this->options);
        $url = OLLAMA_BASE_URL . '/api/chat';
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            return ['error' => 'Ollama API request failed'];
        }

        $json = json_decode($response, true);

        if (!is_array($json)) {
            return [
                'error' => 'Ollama API returned invalid JSON',
                'raw' => $response,
            ];
        }

        $content = $json['message']['content'] ?? $json['response'] ?? '';

        if (trim((string)$content) === '') {
            return [
                'error' => 'Ollama API returned an empty response',
                'raw' => $response,
            ];
        }

        return [
            'text' => $content
        ];
    }
}
