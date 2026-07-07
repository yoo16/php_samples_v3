<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GD Basic Samples</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="bg-slate-100 text-slate-900">
    <main class="mx-auto flex w-full max-w-5xl flex-col justify-center px-5 py-10">
        <header class="mb-8">
            <p class="mb-2 text-sm font-semibold uppercase tracking-wide text-blue-700">PHP GD</p>
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">GD Samples</h1>
            <p class="mt-3 max-w-2xl text-slate-600">
                GDライブラリを使った画像加工の基本サンプルです。画像をアップロードして、加工結果をブラウザ表示またはダウンロードできます。
            </p>
        </header>

        <section class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-6">
            <h2 class="text-lg font-semibold text-amber-950">php.iniでGD拡張を有効にする</h2>
            <p class="mt-3 text-sm leading-6 text-amber-900">
                このサンプルを動かすには、PHPのGD拡張が有効になっている必要があります。
                まずターミナルで次のコマンドを実行し、読み込まれているphp.iniの場所を確認します。
            </p>

            <div class="mt-4 rounded-md bg-white p-4">
                <p class="mb-2 text-sm font-semibold text-slate-800">php.iniの場所を確認</p>
                <pre class="overflow-x-auto rounded bg-slate-900 p-3 text-sm text-slate-100"><code>php --ini</code></pre>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-md bg-white p-4">
                    <p class="mb-2 text-sm font-semibold text-slate-800">php.iniに設定を追加または有効化</p>
                    <pre class="overflow-x-auto rounded bg-slate-900 p-3 text-sm text-slate-100"><code>extension=gd</code></pre>
                    <p class="mt-2 text-xs leading-5 text-slate-600">
                        <code class="font-mono">;extension=gd</code> のように先頭にセミコロンがある場合は、セミコロンを削除して保存します。
                    </p>
                </div>

                <div class="rounded-md bg-white p-4">
                    <p class="mb-2 text-sm font-semibold text-slate-800">GDが有効か確認</p>
                    <pre class="overflow-x-auto rounded bg-slate-900 p-3 text-sm text-slate-100"><code>php -m | findstr gd</code></pre>
                    <p class="mt-2 text-xs leading-5 text-slate-600">
                        Webサーバーを使っている場合は、php.iniを変更したあとにサーバーを再起動します。
                    </p>
                </div>
            </div>
        </section>

        <section class="grid gap-5 md:grid-cols-2">
            <a href="./resize.php"
                class="group rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue-300 hover:shadow-md">
                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4M8 12h8M12 8v8" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold">画像サイズ変更</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    指定した幅と高さに画像をリサイズします。縦横比を維持した変換もできます。
                </p>
                <div class="mt-6 flex items-center text-sm font-semibold text-blue-700">
                    サンプルを開く
                    <span class="ml-2 transition group-hover:translate-x-1">-&gt;</span>
                </div>
            </a>

            <a href="./mosic.php"
                class="group rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-indigo-300 hover:shadow-md">
                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold">モザイク処理</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    画像を小さく縮小してから拡大し、ピクセル風のモザイク画像を作成します。
                </p>
                <div class="mt-6 flex items-center text-sm font-semibold text-indigo-700">
                    サンプルを開く
                    <span class="ml-2 transition group-hover:translate-x-1">-&gt;</span>
                </div>
            </a>
        </section>
    </main>
</body>

</html>
