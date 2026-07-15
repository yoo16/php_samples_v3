<?php
// ============================================================
// api.php - 観光スポットのモックAPI
// フロントから受け取った都市・スタイルを元に
// サンプルの観光スポットを JSON で返す
// ============================================================

// ---------- ① レスポンスヘッダー設定 ----------
header('Content-Type: application/json; charset=utf-8');

// ---------- ② POST パラメータ取得 ----------
$city  = trim($_POST['city']  ?? '');
$style = trim($_POST['style'] ?? '');

// 入力バリデーション
if ($city === '' || $style === '') {
    http_response_code(400);
    echo json_encode(['error' => '都市名と旅行スタイルを入力してください。']);
    exit;
}

// ---------- ③ モックデータ作成 ----------
$spotTemplates = getSpotTemplates($style);

$spots = array_map(function ($spot) use ($city) {
    return [
        'name' => $city . ' ' . $spot['name'],
        'category' => $spot['category'],
        'description' => $spot['description'],
        'address' => $city . ' ' . $spot['address'],
    ];
}, $spotTemplates);

// ---------- ④ フロントへ返却 ----------
echo json_encode([
    'city' => $city,
    'spots' => $spots,
], JSON_UNESCAPED_UNICODE);

function getSpotTemplates(string $style): array
{
    if (str_contains($style, '歴史') || str_contains($style, '文化')) {
        return [
            ['name' => '歴史資料館', 'category' => '歴史', 'description' => '地域の成り立ちや文化をゆっくり学べる定番スポットです。', 'address' => '歴史資料館'],
            ['name' => '旧市街エリア', 'category' => '街歩き', 'description' => '古い建物や路地を歩きながら、街の雰囲気を楽しめます。', 'address' => '旧市街'],
            ['name' => '郷土文化センター', 'category' => '文化', 'description' => '伝統工芸や地域行事を知る入門に向いています。', 'address' => '文化センター'],
            ['name' => '城跡公園', 'category' => '史跡', 'description' => '歴史を感じながら散策でき、写真撮影にも向いています。', 'address' => '城跡公園'],
            ['name' => '伝統工芸館', 'category' => '工芸', 'description' => '地域らしい技術や作品を近くで見られるスポットです。', 'address' => '伝統工芸館'],
        ];
    }

    if (str_contains($style, '自然') || str_contains($style, 'アウトドア')) {
        return [
            ['name' => '中央公園', 'category' => '公園', 'description' => '気軽に散歩や休憩ができる、旅の合間に使いやすい場所です。', 'address' => '中央公園'],
            ['name' => '展望台', 'category' => '景色', 'description' => '街並みを見渡せるので、初めて訪れる旅行者にもおすすめです。', 'address' => '展望台'],
            ['name' => '水辺の散歩道', 'category' => '散策', 'description' => '朝や夕方に歩くと、落ち着いた時間を過ごせます。', 'address' => '川沿い'],
            ['name' => '植物園', 'category' => '自然', 'description' => '季節の花や緑を楽しめる、ゆったりした観光先です。', 'address' => '植物園'],
            ['name' => 'ハイキングコース入口', 'category' => 'アウトドア', 'description' => '半日ほど自然を楽しみたい人に向いた立ち寄り先です。', 'address' => 'ハイキングコース'],
        ];
    }

    if (str_contains($style, 'グルメ') || str_contains($style, '食')) {
        return [
            ['name' => '市場通り', 'category' => 'グルメ', 'description' => '地元の食材や軽食をまとめて楽しみやすいエリアです。', 'address' => '市場'],
            ['name' => '郷土料理レストラン', 'category' => 'レストラン', 'description' => '初めての街で地域らしい味を試すのに向いています。', 'address' => '郷土料理'],
            ['name' => 'カフェストリート', 'category' => 'カフェ', 'description' => '休憩しながら街歩きの計画を立てやすいスポットです。', 'address' => 'カフェ'],
            ['name' => 'スイーツ専門店', 'category' => 'スイーツ', 'description' => '旅のおやつやおみやげ探しに立ち寄りやすいお店です。', 'address' => 'スイーツ'],
            ['name' => '駅前フードエリア', 'category' => '食べ歩き', 'description' => '短時間でも複数の店を比較しながら楽しめます。', 'address' => '駅前'],
        ];
    }

    if (str_contains($style, 'ショッピング')) {
        return [
            ['name' => '駅前ショッピングモール', 'category' => '買い物', 'description' => '天候に左右されにくく、食事や休憩もしやすい場所です。', 'address' => '駅前 ショッピングモール'],
            ['name' => '商店街', 'category' => '街歩き', 'description' => '地元らしい店を見つけながら散策できます。', 'address' => '商店街'],
            ['name' => 'クラフトショップ', 'category' => '雑貨', 'description' => '地域らしい小物やおみやげを探すのに向いています。', 'address' => 'クラフトショップ'],
            ['name' => '百貨店エリア', 'category' => '百貨店', 'description' => '定番のおみやげや食品をまとめて見つけやすい場所です。', 'address' => '百貨店'],
            ['name' => 'セレクトショップ通り', 'category' => 'ファッション', 'description' => '服や雑貨を見ながら街の雰囲気も楽しめます。', 'address' => 'セレクトショップ'],
        ];
    }

    if (str_contains($style, 'アート') || str_contains($style, '美術館')) {
        return [
            ['name' => '市立美術館', 'category' => '美術館', 'description' => '地域の作品や企画展を楽しめる落ち着いたスポットです。', 'address' => '市立美術館'],
            ['name' => '現代アートギャラリー', 'category' => 'ギャラリー', 'description' => '短時間でも作品鑑賞を楽しみやすい小規模展示です。', 'address' => 'ギャラリー'],
            ['name' => 'デザインミュージアム', 'category' => 'デザイン', 'description' => '建築やプロダクトに興味がある人に向いています。', 'address' => 'デザインミュージアム'],
            ['name' => 'アートイベント会場', 'category' => 'イベント', 'description' => '開催時期が合えば、地域の表現に触れられます。', 'address' => 'アートイベント'],
            ['name' => '公共アート散策路', 'category' => '街歩き', 'description' => '屋外作品を見ながら街の風景も一緒に楽しめます。', 'address' => '公共アート'],
        ];
    }

    return [
        ['name' => 'ファミリー公園', 'category' => '家族向け', 'description' => '子どもと一緒に休憩しながら遊びやすい広い公園です。', 'address' => '公園'],
        ['name' => '科学館', 'category' => '体験', 'description' => '展示を見ながら楽しく学べる屋内スポットです。', 'address' => '科学館'],
        ['name' => '水族館', 'category' => 'レジャー', 'description' => '天候に左右されにくく、家族で過ごしやすい定番です。', 'address' => '水族館'],
        ['name' => 'キッズ向けカフェ', 'category' => 'カフェ', 'description' => '食事と休憩を取りながら予定を調整しやすい場所です。', 'address' => 'キッズカフェ'],
        ['name' => '体験工房', 'category' => 'ものづくり', 'description' => '旅の思い出になる簡単な制作体験ができます。', 'address' => '体験工房'],
    ];
}
