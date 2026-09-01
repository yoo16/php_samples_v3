<?php
// GET パラメータから URL を取得
$url = trim($_GET['url'] ?? '');
// ダウンロード時のファイル名
$filename = 'qr_' . md5($url) . '.png';
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-sky-600 px-6 py-5">
                <h1 class="text-2xl font-bold text-white">QR Code Generator</h1>
                <p class="text-sky-100 text-sm mt-1">endroid/qr-code で QR コードを生成</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- 入力フォーム -->
                <form method="get" class="space-y-3">
                    <label class="block font-medium text-slate-700">URL を入力してください</label>
                    <div class="flex gap-2">
                        <input type="text" name="url" value="<?= htmlspecialchars($url) ?>"
                            placeholder="https://example.com" required
                            class="flex-1 rounded-xl border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <button type="submit"
                            class="rounded-xl bg-sky-600 px-5 py-2 font-semibold text-white hover:bg-sky-700 transition">
                            生成
                        </button>
                    </div>
                </form>

                <?php if ($url !== ''): ?>
                    <!-- 生成結果 -->
                    <section class="flex flex-col items-center gap-4 rounded-xl border border-slate-200 p-4">
                        <img id="qrImage" src="qrcode.php?url=<?= urlencode($url) ?>" alt="QRコード"
                            class="border border-slate-200 rounded">
                        <button id="downloadBtn"
                            class="rounded-xl bg-sky-600 px-5 py-2 font-semibold text-white hover:bg-sky-700 transition">
                            QR コードを保存
                        </button>
                    </section>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        const btn = document.getElementById('downloadBtn');
        if (btn) {
            btn.addEventListener('click', () => {
                const img = document.getElementById('qrImage');
                fetch(img.src)
                    .then(res => res.blob())
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = '<?= $filename ?>';
                        a.click();
                        window.URL.revokeObjectURL(url);
                    });
            });
        }
    </script>
</body>

</html>
