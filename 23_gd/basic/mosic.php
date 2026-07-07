<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image']['tmp_name'];
    $pixelSize = max(2, intval($_POST['pixel'] ?? 20));
    $mode = $_POST['mode'] ?? 'show';

    if (!file_exists($file)) {
        die('ファイルが見つかりません。');
    }

    $uploadFile = file_get_contents($file);
    $src = imagecreatefromstring($uploadFile);

    if (!$src) {
        die('画像ファイルを読み込めませんでした。');
    }

    $width = imagesx($src);
    $height = imagesy($src);
    $smallW = max(1, intval($width / $pixelSize));
    $smallH = max(1, intval($height / $pixelSize));

    $small = imagecreatetruecolor($smallW, $smallH);
    imagecopyresampled($small, $src, 0, 0, 0, 0, $smallW, $smallH, $width, $height);

    $pixelated = imagecreatetruecolor($width, $height);
    imagecopyresized($pixelated, $small, 0, 0, 0, 0, $width, $height, $smallW, $smallH);

    header('Content-Type: image/png');

    if ($mode === 'download') {
        header('Content-Disposition: attachment; filename="pixelated.png"');
    }

    imagepng($pixelated);
    imagedestroy($src);
    imagedestroy($small);
    imagedestroy($pixelated);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>モザイク処理サンプル</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="min-h-screen bg-slate-100 px-4 py-10 text-slate-900">
    <main class="mx-auto max-w-2xl">
        <header class="mb-6">
            <p class="mb-2 text-sm font-semibold text-indigo-700">PHP GD Basic</p>
            <h1 class="text-3xl font-bold">モザイク処理サンプル</h1>
            <a href="./" class="mt-4 inline-flex items-center text-sm font-semibold text-indigo-700 hover:text-indigo-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                戻る
            </a>
            <p class="mt-3 text-slate-600">
                アップロードした画像を一度小さく縮小してから元のサイズに拡大し、ピクセル風のモザイク画像を作成します。
            </p>
        </header>

        <form method="post" enctype="multipart/form-data" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="space-y-5">
                <div>
                    <label for="image" class="mb-2 block text-sm font-semibold">画像ファイル</label>
                    <input id="image" type="file" name="image" accept="image/*" required
                        class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm file:mr-4 file:rounded-md file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700">
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between gap-4">
                        <label for="pixelRange" class="block text-sm font-semibold">モザイクの粗さ</label>
                        <span class="rounded-md bg-indigo-50 px-2.5 py-1 text-sm font-semibold text-indigo-700">
                            <span id="pixelValue">20</span> px
                        </span>
                    </div>
                    <input id="pixelRange" type="range" name="pixel" value="20" min="2" max="100"
                        class="w-full accent-indigo-600">
                    <div class="mt-2 flex justify-between text-xs text-slate-500">
                        <span>細かい</span>
                        <span>粗い</span>
                    </div>
                </div>

                <div>
                    <label for="mode" class="mb-2 block text-sm font-semibold">出力方法</label>
                    <select id="mode" name="mode" class="w-full rounded-md border border-slate-300 bg-white px-3 py-2">
                        <option value="show">ブラウザに表示</option>
                        <option value="download">ダウンロード</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full rounded-md bg-indigo-600 px-4 py-3 font-semibold text-white hover:bg-indigo-700">
                    モザイク処理する
                </button>
            </div>
        </form>
    </main>

    <script>
        const range = document.getElementById('pixelRange');
        const valueDisplay = document.getElementById('pixelValue');

        range.addEventListener('input', () => {
            valueDisplay.textContent = range.value;
        });
    </script>
</body>

</html>
