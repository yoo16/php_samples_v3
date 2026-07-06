<?php
// ============================================================
// api/travel/create.php
// JS からのリクエストを受け取り、Gemini API で旅行プラン(JSON)を生成して返す
// JS -> PHP API -> Gemini API -> JS (CSR)
// ============================================================
require_once __DIR__ . '/../../app.php';
require_once __DIR__ . '/../../services/GeminiService.php';

header('Content-Type: application/json; charset=UTF-8');

// POST 以外は拒否
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POSTメソッドで送信してください'], JSON_UNESCAPED_UNICODE);
    exit;
}

// JSONボディを取得
$input = json_decode(file_get_contents('php://input'), true) ?? [];

$departure_date = trim($input['departure_date'] ?? ''); // 出発日
$days = (int)($input['days'] ?? 0); // 日数
$from = trim($input['from'] ?? ''); // 出発場所
$to = trim($input['to'] ?? '');// 到着場所

// バリデーション
if ($departure_date === '' || $days < 1 || $days > 14 || $from === '' || $to === '') {
    http_response_code(400);
    echo json_encode(['error' => '日程・出発場所・到着場所をすべて入力してください'], JSON_UNESCAPED_UNICODE);
    exit;
}

// プロンプト作成（返してほしいJSONの構造を明示する）
$prompt = <<<PROMPT
あなたは旅行プランナーです。以下の条件で旅行プランを日本語で作成してください。

- 出発日: {$departure_date}
- 日数: {$days}日間
- 出発場所: {$from}
- 到着場所（目的地）: {$to}

必ず次のJSON構造だけを返してください。
{
  "title": "プランのタイトル",
  "summary": "プラン全体の概要（100文字程度）",
  "days": [
    {
      "day": 1,
      "date": "YYYY-MM-DD",
      "theme": "その日のテーマ",
      "schedule": [
        { "time": "09:00", "place": "場所名", "activity": "行動の説明" }
      ]
    }
  ],
  "tips": ["旅行のアドバイス1", "旅行のアドバイス2"]
}
PROMPT;

// Gemini API にリクエスト
$gemini = new GeminiService();
$plan = $gemini->generateJson($prompt);

if ($plan === null) {
    http_response_code(502);
    echo json_encode(['error' => 'プランの生成に失敗しました。もう一度お試しください'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode($plan, JSON_UNESCAPED_UNICODE);