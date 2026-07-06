<?php
// Travel plan generation endpoint.
// Receives trip inputs, asks Ollama for a JSON plan, and returns a decoded object.

require_once __DIR__ . '/../../app.php';
require_once __DIR__ . '/../services/OllamaService.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'POST request required'], JSON_UNESCAPED_UNICODE);
  exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$departure_date = trim($input['departure_date'] ?? '');
$days = (int)($input['days'] ?? 0);
$from = trim($input['from'] ?? '');
$to = trim($input['to'] ?? '');

if ($departure_date === '' || $days < 1 || $days > 14 || $from === '' || $to === '') {
  http_response_code(400);
  echo json_encode(['error' => 'departure_date, days, from, and to are required'], JSON_UNESCAPED_UNICODE);
  exit;
}

$prompt = <<<PROMPT
あなたはるる旅行プランナーです。
日本語で、以下の条件に基づいて旅行プランを作成してください。

- Departure date: {$departure_date}
- Days: {$days}
- From: {$from}
- To: {$to}

Return only valid JSON in this shape:
{
  "title": "Trip title",
  "summary": "Short summary",
  "days": [
    {
      "day": 1,
      "date": "YYYY-MM-DD",
      "theme": "Theme for the day",
      "schedule": [
        { "time": "09:00", "place": "Place name", "activity": "Activity" }
      ]
    }
  ],
  "tips": ["Tip 1", "Tip 2"]
}
PROMPT;

$ollama = new OllamaService();
$result = $ollama->chat($prompt);

if (isset($result['error'])) {
  http_response_code(502);
  echo json_encode(['error' => $result['error']], JSON_UNESCAPED_UNICODE);
  exit;
}

$planText = trim((string)($result['text'] ?? ''));

if ($planText === '') {
  http_response_code(502);
  echo json_encode(['error' => 'Empty response from Ollama'], JSON_UNESCAPED_UNICODE);
  exit;
}

// Ollama can wrap JSON in code fences; strip them before decoding.
$planText = preg_replace('/^```(?:json)?\s*/i', '', $planText);
$planText = preg_replace('/\s*```$/', '', $planText);

$plan = json_decode($planText, true);

if (!is_array($plan) || json_last_error() !== JSON_ERROR_NONE) {
  http_response_code(502);
  echo json_encode([
    'error' => 'Model response was not valid JSON',
    'raw' => $planText,
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode($plan, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
