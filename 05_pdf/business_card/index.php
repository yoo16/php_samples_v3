<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/PdfGenerator.php';
// 設定ファイルの読み込み
require_once __DIR__ . '/config.php';

// PdfGeneratorクラスの使用を宣言
use App\PdfGenerator;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gen = new PdfGenerator($config);
    $gen->generate($_POST);
    exit;
}

$name = '東京 太郎';
$title = 'SENIOR ENGINEER';
$email = 'tokyo@example.com';
$web = 'https://tokyo.com';
$tel = '090-0000-0000';
$color_name = '#172033';
$color_title = '#0f766e';
$color_info = '#475569';
$bg_zoom = 118;
$bg_x = 50;
$bg_y = 50;
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>名刺ジェネレーター</title>
    <link rel="stylesheet" href="css/editor.css?<?= time() ?>">
    <link rel="stylesheet" href="css/pdf.css?<?= time() ?>">
</head>

<body>

    <aside class="editor-sidebar">
        <header class="editor-header">
            <p>PDF Sample</p>
            <h1>名刺ジェネレーター</h1>
        </header>
        <form method="POST" class="editor-form" enctype="multipart/form-data">
            <section class="form-section">
                <h2>プロフィール</h2>
                <div class="editor-row">
                    <div class="input-group main-input">
                        <label for="in_name">氏名</label>
                        <input type="text" name="name" id="in_name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" oninput="update()">
                    </div>
                    <div class="input-group color-input">
                        <label for="in_color_name">色</label>
                        <input type="color" name="color_name" id="in_color_name" value="<?= htmlspecialchars($color_name, ENT_QUOTES, 'UTF-8') ?>" oninput="update()">
                    </div>
                </div>
                <div class="editor-row">
                    <div class="input-group main-input">
                        <label for="in_title">役職</label>
                        <input type="text" name="title" id="in_title" value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" oninput="update()">
                    </div>
                    <div class="input-group color-input">
                        <label for="in_color_title">色</label>
                        <input type="color" name="color_title" id="in_color_title" value="<?= htmlspecialchars($color_title, ENT_QUOTES, 'UTF-8') ?>" oninput="update()">
                    </div>
                </div>
            </section>

            <section class="form-section">
                <div class="section-heading">
                    <h2>連絡先</h2>
                    <input type="color" name="color_info" id="in_color_info" value="<?= htmlspecialchars($color_info, ENT_QUOTES, 'UTF-8') ?>" oninput="update()" aria-label="連絡先の文字色">
                </div>
                <div class="input-group">
                    <label for="in_email">Email</label>
                    <input type="email" name="email" id="in_email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" oninput="update()">
                </div>
                <div class="input-group">
                    <label for="in_web">Web</label>
                    <input type="text" name="web" id="in_web" value="<?= htmlspecialchars($web, ENT_QUOTES, 'UTF-8') ?>" oninput="update()">
                </div>
                <div class="input-group">
                    <label for="in_tel">Tel</label>
                    <input type="text" name="tel" id="in_tel" value="<?= htmlspecialchars($tel, ENT_QUOTES, 'UTF-8') ?>" oninput="update()">
                </div>
            </section>

            <section class="form-section">
                <h2>背景</h2>
                <div class="input-group">
                    <label for="in_bg">背景画像</label>
                    <input type="file" name="bg_image" id="in_bg" accept="image/*" onchange="previewImage(this)">
                </div>
                <input type="hidden" name="bg_base64" id="bg_base64">

                <div class="bg-manager">
                    <div class="slider-row">
                        <label for="in_bg_zoom">ズーム</label>
                        <input type="range" name="bg_zoom" id="in_bg_zoom" min="40" max="260" value="<?= (int) $bg_zoom ?>" oninput="update()">
                    </div>
                    <div class="slider-row">
                        <label for="in_bg_x">位置 X</label>
                        <input type="range" name="bg_x" id="in_bg_x" min="0" max="100" value="<?= (int) $bg_x ?>" oninput="update()">
                    </div>
                    <div class="slider-row">
                        <label for="in_bg_y">位置 Y</label>
                        <input type="range" name="bg_y" id="in_bg_y" min="0" max="100" value="<?= (int) $bg_y ?>" oninput="update()">
                    </div>
                </div>
            </section>

            <button type="submit" class="btn-download">PDFをダウンロード</button>
        </form>
    </aside>

    <main class="preview-main">
        <div class="preview-label">Preview / 91mm x 55mm</div>
        <div class="preview-canvas">
            <?php
            include 'templates/card.php';
            ?>
        </div>
    </main>

    <script src="js/app.js"></script>
</body>

</html>
