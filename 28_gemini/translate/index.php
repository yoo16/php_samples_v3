<?php
require_once 'lib/Lang.php';
$langs = Lang::$languages;

$defaultFromLang = 'ja-JP';
$defaultToLang = 'en-US';

function selected(mixed $value, mixed $selected): string
{
    return ($value == $selected) ? 'selected' : '';
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Translate</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-white text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-6xl flex-col gap-6 px-5 py-6 sm:px-8 lg:px-10">
        <header class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-sky-600">Gemini API</p>
                <h1 class="mt-1 text-3xl font-bold text-slate-950">AI Translate</h1>
            </div>
            <nav class="flex flex-wrap gap-2 text-sm font-semibold">
                <a href="index.php" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-slate-700 shadow-sm transition hover:border-sky-300 hover:text-sky-700">メニュー</a>
                <a href="chat.php" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-slate-700 shadow-sm transition hover:border-sky-300 hover:text-sky-700">Chat</a>
                <a href="whats_photo.php" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-slate-700 shadow-sm transition hover:border-sky-300 hover:text-sky-700">What's Photo</a>
            </nav>
        </header>

        <div class="grid flex-1 gap-6 lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
            <section class="self-start rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">翻訳</h2>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="grid gap-3">
                        <div class="grid gap-3 sm:grid-cols-[1fr_auto_1fr] sm:items-end">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">翻訳前</span>
                                <select id="fromLang" class="w-full rounded-lg border border-slate-200 bg-slate-50 p-3 text-slate-800 outline-none transition focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-100">
                                    <?php foreach ($langs as $lang): ?>
                                        <option value="<?= $lang['code'] ?>" <?= selected($defaultFromLang, $lang['code']) ?>><?= $lang['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </label>

                            <button type="button" class="rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-sky-300 hover:text-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-100" onclick="swapLanguages()">
                                &#x21C4;
                            </button>

                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">翻訳後</span>
                                <select id="toLang" class="w-full rounded-lg border border-slate-200 bg-slate-50 p-3 text-slate-800 outline-none transition focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-100">
                                    <?php foreach ($langs as $lang): ?>
                                        <option value="<?= $lang['code'] ?>" <?= selected($defaultToLang, $lang['code']) ?>><?= $lang['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </label>
                        </div>
                    </div>

                    <label class="block">
                        <span class="mb-2 block text-sm font-medium text-slate-700">テキスト</span>
                        <textarea id="result" rows="8" class="w-full resize-y rounded-lg border border-slate-200 bg-slate-50 p-4 leading-7 text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:ring-2 focus:ring-sky-100" placeholder="翻訳したいテキストを入力してください"></textarea>
                    </label>

                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
                        <button id="micButton" type="button" class="rounded-lg border border-slate-200 bg-white px-5 py-3 font-semibold text-slate-700 shadow-sm transition hover:border-emerald-300 hover:text-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-100" onclick="startSpeech()">
                            音声入力
                        </button>
                        <button id="startButton" type="button" class="rounded-lg bg-sky-600 px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-200">
                            翻訳
                        </button>
                    </div>

                    <p id="status" class="min-h-6 text-sm font-semibold text-red-600"></p>
                </div>
            </section>

            <section class="flex min-h-[34rem] flex-col rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <h2 class="text-lg font-semibold text-slate-950">会話</h2>
                    <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-500">History</span>
                </div>
                <div id="chatHistory" class="flex flex-1 flex-col space-y-4 overflow-y-auto bg-slate-50/70 p-6">
                    <div id="emptyHistory" class="rounded-lg border border-dashed border-slate-200 bg-white p-5 text-sm leading-7 text-slate-500">
                        翻訳結果がここに表示されます。
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script src="js/app.js" defer></script>
</body>

</html>
