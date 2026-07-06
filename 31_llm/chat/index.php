<?php
// 共通ブートストラップ（.env 読み込み・定数定義）
require_once __DIR__ . '/../app.php';
// OllamaService クラスを読み込む
require_once __DIR__ . '/services/OllamaService.php';

$prompt = $_POST['prompt'] ?? '';
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ollama = new OllamaService();
    $result = $ollama->chat($prompt);
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ollama Chat</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.2.5/dist/purify.min.js"></script>
</head>

<body class="min-h-screen bg-slate-100 p-6">
    <main class="mx-auto max-w-4xl space-y-6">
        <h1 class="text-2xl font-bold text-slate-900">Ollama Chat</h1>

        <section class="rounded-lg bg-white p-6 shadow">
            <form action="" method="post" class="space-y-4" data-loading-message="Ollama に質問を送信しています。しばらくお待ちください。">
                <label class="block">
                    <span class="mb-2 block text-sm font-medium text-slate-700">質問内容</span>
                    <textarea name="prompt" rows="5" class="w-full rounded-lg border border-slate-300 p-3 leading-7 text-slate-700 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100" placeholder="質問を入力してください..."><?= htmlspecialchars($prompt) ?></textarea>
                </label>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-lg bg-sky-600 px-5 py-2.5 font-semibold text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-200">送信</button>
                </div>
            </form>
        </section>

        <?php if ($result['text']): ?>
            <section class="rounded-lg border border-slate-200 bg-white p-6 shadow">
                <h2 class="text-lg font-semibold text-slate-900">Ollamaの回答</h2>
                <div id="ollama-response" class="prose prose-slate mt-5 max-w-none rounded-lg border border-slate-200 bg-slate-50 p-5 prose-a:text-sky-600"></div>
                <script id="ollama-result" type="application/json">
                    <?= json_encode($result['text'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
                </script>
            </section>
        <?php endif; ?>
    </main>

    <?php if ($result['text']): ?>
        <script>
            const responseElement = document.getElementById('ollama-response');
            const markdownElement = document.getElementById('ollama-result');

            if (responseElement && markdownElement) {
                const markdownText = JSON.parse(markdownElement.textContent);

                if (window.marked && window.DOMPurify) {
                    marked.setOptions({
                        breaks: true,
                        gfm: true
                    });
                    responseElement.innerHTML = DOMPurify.sanitize(marked.parse(markdownText));
                } else {
                    responseElement.textContent = markdownText;
                    responseElement.classList.add('whitespace-pre-wrap');
                }
            }
        </script>
    <?php endif; ?>
    <?php include 'components/loading_modal.php'; ?>
</body>

</html>