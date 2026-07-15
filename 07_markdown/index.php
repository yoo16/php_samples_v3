<?php
require '../vendor/autoload.php';

$file = './data/blog.md';
$markdown = '';
$html = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['markdown'])) {
    $markdown = $_POST['markdown'];
    file_put_contents($file, $markdown); // 上書き保存
    $message = '保存しました。';
} elseif (file_exists($file)) {
    $markdown = file_get_contents($file);
}

$parsedown = new Parsedown();
$html = $parsedown->text($markdown);
$escapedMarkdown = htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Markdown Editor</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
</head>

<body class="min-h-screen bg-slate-100">

    <!-- ===== ヘッダー ===== -->
    <header class="border-b border-slate-200 bg-white px-6 py-4 shadow-sm">
        <div class="mx-auto flex max-w-6xl items-center gap-3">
            <span class="text-2xl">📝</span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-sky-600">Parsedown</p>
                <h1 class="text-xl font-bold text-slate-900">Markdown Editor</h1>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-6 py-8">

        <?php if ($message): ?>
            <p class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                ✅ <?= $message ?>
            </p>
        <?php endif; ?>

        <div class="grid gap-6 lg:grid-cols-2">

            <!-- ===== 編集フォーム ===== -->
            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
                    <h2 class="text-sm font-semibold text-slate-700">Markdown</h2>
                    <span class="text-xs text-slate-400">data/blog.md</span>
                </div>
                <form method="POST" class="p-5">
                    <textarea name="markdown" spellcheck="false"
                        class="h-[480px] w-full resize-y rounded-lg border border-slate-300 bg-slate-50 p-4 font-mono text-sm leading-6 text-slate-800 focus:border-sky-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-100"><?= $escapedMarkdown ?></textarea>
                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                            class="rounded-lg bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-200">
                            保存する
                        </button>
                    </div>
                </form>
            </section>

            <!-- ===== プレビュー ===== -->
            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
                    <h2 class="text-sm font-semibold text-slate-700">プレビュー</h2>
                    <span class="text-xs text-slate-400">HTML</span>
                </div>
                <div class="prose prose-slate max-w-none p-6 prose-pre:bg-slate-800 prose-a:text-sky-600">
                    <?= $html ?>
                </div>
            </section>

        </div>
    </main>
</body>

</html>
