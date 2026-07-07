<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image']['tmp_name'];
    $fileType = $_FILES['image']['type']; // MIMEタイプを取得 (image/jpeg, image/png など)
    $mode = $_POST['mode'] ?? 'show';
    $targetWidth = max(1, intval($_POST['width'] ?? 300));
    $targetHeight = max(1, intval($_POST['height'] ?? 300));
    $keepAspect = isset($_POST['keep_aspect']);

    if (!file_exists($file)) {
        die('ファイルが見つかりません。');
    }

    // 1. 画像タイプを判別して読み込み
    if ($fileType === 'image/jpeg' || $fileType === 'image/jpg') {
        // JPEGの場合
        $src = imagecreatefromjpeg($file);
        $outputType = 'jpeg';
    } elseif ($fileType === 'image/png') {
        // PNGの場合
        $src = imagecreatefrompng($file);
        $outputType = 'png';
    } else {
        die('対応していないファイル形式です。JPEGまたはPNGをアップロードしてください。');
    }

    if (!$src) {
        die('画像ファイルを読み込めませんでした。');
    }

    // 画像の元の幅と高さを取得
    $width = imagesx($src);
    $height = imagesy($src);

    // アスペクト比を維持する場合、リサイズ後の幅と高さを計算
    if ($keepAspect) {
        $ratio = min($targetWidth / $width, $targetHeight / $height);
        $targetWidth = max(1, intval($width * $ratio));
        $targetHeight = max(1, intval($height * $ratio));
    }

    // リサイズ用の土台画像を作成
    $resized = imagecreatetruecolor($targetWidth, $targetHeight);

    // 2. 形式に応じた透過・背景処理
    if ($outputType === 'png') {
        // PNGの場合は透過を維持
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, $transparent);
    } else {
        // JPEGの場合は透過できないため、背景を「白」で塗りつぶす（黒になるのを防ぐ）
        $white = imagecolorallocate($resized, 255, 255, 255);
        imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, $white);
    }

    // 元の画像をリサイズして新しい画像にコピー
    imagecopyresampled(
        $resized,
        $src,
        0,
        0,
        0,
        0,
        $targetWidth,
        $targetHeight,
        $width,
        $height
    );

    // 3. 形式に応じた出力ヘッダーの設定
    if ($outputType === 'png') {
        header('Content-Type: image/png');
        if ($mode === 'download') {
            header('Content-Disposition: attachment; filename="resized.png"');
        }
        imagepng($resized);
    } else {
        header('Content-Type: image/jpeg');
        if ($mode === 'download') {
            header('Content-Disposition: attachment; filename="resized.jpg"');
        }
        imagejpeg($resized, null, 90); // クオリティを90に設定（0〜100）
    }

    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>画像リサイズサンプル</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="min-h-screen bg-slate-100 px-4 py-10 text-slate-900">
    <main class="mx-auto max-w-2xl">
        <header class="mb-6">
            <p class="mb-2 text-sm font-semibold text-blue-700">PHP GD Basic</p>
            <h1 class="text-3xl font-bold">画像サイズ変更サンプル</h1>
            <a href="./" class="mt-4 inline-flex items-center text-sm font-semibold text-blue-700 hover:text-blue-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                戻る
            </a>
            <p class="mt-3 text-slate-600">アップロードした画像（JPEG/PNG）を、指定した幅と高さにリサイズします。</p>
        </header>

        <form method="post" enctype="multipart/form-data" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="space-y-5">
                <div>
                    <label for="image" class="mb-2 block text-sm font-semibold">画像ファイル</label>
                    <input id="image" type="file" name="image" accept="image/png, image/jpeg" required
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="width" class="mb-2 block text-sm font-semibold">幅</label>
                        <div class="flex items-center gap-2">
                            <input id="width" type="number" name="width" value="400" min="1" max="4000" required
                                class="w-full rounded-md border border-slate-300 px-3 py-2">
                            <span class="text-sm text-slate-500">px</span>
                        </div>
                    </div>

                    <div>
                        <label for="height" class="mb-2 block text-sm font-semibold">高さ</label>
                        <div class="flex items-center gap-2">
                            <input id="height" type="number" name="height" value="300" min="1" max="4000" required
                                class="w-full rounded-md border border-slate-300 px-3 py-2">
                            <span class="text-sm text-slate-500">px</span>
                        </div>
                    </div>
                </div>

                <label class="flex items-center gap-3 rounded-md bg-slate-50 p-4 text-sm font-medium">
                    <input type="checkbox" name="keep_aspect" value="1" checked
                        class="h-4 w-4 rounded border-slate-300 text-blue-600">
                    縦横比を維持する
                </label>

                <div>
                    <label for="mode" class="mb-2 block text-sm font-semibold">出力方法</label>
                    <select id="mode" name="mode" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2">
                        <option value="show">ブラウザに表示</option>
                        <option value="download">ダウンロード</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full rounded-md bg-blue-600 px-4 py-3 font-semibold text-white hover:bg-blue-700">
                    リサイズする
                </button>
            </div>
        </form>
    </main>
</body>

</html>