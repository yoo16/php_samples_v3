<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemini API Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100">
    <main class="mx-auto flex min-h-screen max-w-4xl items-center px-6 py-10">
        <section class="w-full rounded-lg bg-white p-8 shadow">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-wide text-sky-600">Gemini API</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">メニュー</h1>
                <p class="mt-2 text-slate-600">利用する機能を選択してください。</p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <a href="about.php" class="group rounded-lg border border-slate-200 p-6 transition hover:border-sky-300 hover:bg-sky-50">
                    <h2 class="text-xl font-semibold text-slate-900 group-hover:text-sky-700">Gemini API ガイド</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Gemini API の概要・利用法・API キー作成手順を確認します。</p>
                    <span class="mt-5 inline-flex items-center text-sm font-semibold text-sky-600">
                        ガイドを見る
                        <span class="ml-2" aria-hidden="true">&rarr;</span>
                    </span>
                </a>

                <a href="chat/" class="group rounded-lg border border-slate-200 p-6 transition hover:border-sky-300 hover:bg-sky-50">
                    <h2 class="text-xl font-semibold text-slate-900 group-hover:text-sky-700">Chat</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">テキストで質問して、Gemini の回答を表示します。</p>
                    <span class="mt-5 inline-flex items-center text-sm font-semibold text-sky-600">
                        chat/ を開く
                        <span class="ml-2" aria-hidden="true">&rarr;</span>
                    </span>
                </a>

                <a href="photo/" class="group rounded-lg border border-slate-200 p-6 transition hover:border-sky-300 hover:bg-sky-50">
                    <h2 class="text-xl font-semibold text-slate-900 group-hover:text-sky-700">What's Photo</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">画像をアップロードして、内容を解析します。</p>
                    <span class="mt-5 inline-flex items-center text-sm font-semibold text-sky-600">
                        photo/ を開く
                        <span class="ml-2" aria-hidden="true">&rarr;</span>
                    </span>
                </a>

                <a href="translate/" class="group rounded-lg border border-slate-200 p-6 transition hover:border-sky-300 hover:bg-sky-50">
                    <h2 class="text-xl font-semibold text-slate-900 group-hover:text-sky-700">AI Translate</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">AIでテキストを翻訳します。</p>
                    <span class="mt-5 inline-flex items-center text-sm font-semibold text-sky-600">
                        translate/ を開く
                        <span class="ml-2" aria-hidden="true">&rarr;</span>
                    </span>
                </a>

                <a href="travel/" class="group rounded-lg border border-slate-200 p-6 transition hover:border-sky-300 hover:bg-sky-50">
                    <h2 class="text-xl font-semibold text-slate-900 group-hover:text-sky-700">Travel Planner</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">AIで旅行プランを生成します。</p>
                    <span class="mt-5 inline-flex items-center text-sm font-semibold text-sky-600">
                        travel/ を開く
                        <span class="ml-2" aria-hidden="true">&rarr;</span>
                    </span>
                </a>

            </div>
        </section>
    </main>
</body>

</html>
