<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image']['tmp_name'];
    $pixelSize = intval($_POST['pixel']) ?: 20;
    $mode = $_POST['mode'] ?? 'show';
    $mode = "show";

    if (!file_exists($file)) {
        die('ファイルが見つかりません。');
    }

    $upload_file = file_get_contents($file);
    $src = imagecreatefromstring($upload_file);
    $width = imagesx($src);
    $height = imagesy($src);

    $smallW = intval($width / $pixelSize);
    $smallH = intval($height / $pixelSize);

    $small = imagecreatetruecolor($smallW, $smallH);
    imagecopyresampled($small, $src, 0, 0, 0, 0, $smallW, $smallH, $width, $height);

    $pixelated = imagecreatetruecolor($width, $height);
    imagecopyresized($pixelated, $small, 0, 0, 0, 0, $width, $height, $smallW, $smallH);

    header("Content-Type: image/png");

    if ($mode === 'download') {
        header("Content-Disposition: attachment; filename=\"pixelated.png\"");
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
    <title>ピクセルアート画像ジェネレーター</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 text-gray-800 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white shadow-lg rounded-xl p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-6 text-center">🟪 ピクセル風画像ジェネレーター</h2>
        <form method="post" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label class="block mb-1 font-medium">画像を選択:</label>
                <input type="file" name="image" accept="image/*" required
                    class="w-full p-2 border rounded border-gray-300">
            </div>
            <div>
                <label class="block mb-1 font-medium">ピクセルの粗さ (推奨:10〜50):</label>
                <div class="flex items-center gap-3">
                    <input type="range" id="pixelRange" name="pixel" value="20" min="2" max="100"
                        class="flex-1 accent-indigo-600">
                    <span id="pixelValue" class="font-semibold text-indigo-700">20</span> px
                </div>
            </div>
            <div>
                <label class="block mb-1 font-medium">出力方法:</label>
                <select name="mode"
                    class="w-full p-2 border rounded border-gray-300 bg-white">
                    <option value="show">表示</option>
                    <option value="download">ダウンロード</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">
                    ピクセル化する
                </button>
            </div>
        </form>
    </div>

    <script>
        const range = document.getElementById('pixelRange');
        const valueDisplay = document.getElementById('pixelValue');
        range.addEventListener('input', () => {
            valueDisplay.textContent = range.value;
        });
    </script>
</body>

</html>