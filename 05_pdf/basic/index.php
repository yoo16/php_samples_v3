<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Basic</title>
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <main class="mx-auto flex min-h-screen max-w-5xl flex-col justify-center px-6 py-12">
        <div class="mb-8">
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-blue-600">PHP PDF Samples</p>
            <h1 class="text-4xl font-bold tracking-tight text-slate-950">PDF Basic</h1>
            <p class="mt-3 max-w-2xl text-slate-600">
                HTML と CSS から PDF を生成する基本サンプルです。ライブラリごとの書き方と出力結果を確認できます。
            </p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <a href="dompdf_sample.php" target="_blank"
                class="group block rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md">
                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-lg bg-blue-50 text-xl font-bold text-blue-600">
                    D
                </div>
                <h2 class="text-xl font-bold text-slate-950">dompdf</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    シンプルな HTML を A4 縦向きの PDF として表示します。PDF 生成の基本的な流れを確認するサンプルです。
                </p>
                <div class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-blue-600">
                    PDFを開く
                    <span class="transition group-hover:translate-x-1">-&gt;</span>
                </div>
            </a>

            <a href="mpdf_sample.php" target="_blank"
                class="group block rounded-lg border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-50 text-xl font-bold text-emerald-600">
                    M
                </div>
                <h2 class="text-xl font-bold text-slate-950">mPDF</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">
                    日本語モードで PDF を生成します。日本語を含む帳票や印刷物を作るときの入口になるサンプルです。
                </p>
                <div class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-emerald-600">
                    PDFを開く
                    <span class="transition group-hover:translate-x-1">-&gt;</span>
                </div>
            </a>
        </div>

        <div class="mt-8 rounded-lg border border-slate-200 bg-white p-5 text-sm text-slate-600">
            <p class="font-semibold text-slate-800">確認ポイント</p>
            <p class="mt-2">
                どちらのリンクも別タブで PDF を表示します。表示されない場合は、Composer の依存関係と PHP のエラー表示を確認してください。
            </p>
        </div>
    </main>
</body>

</html>
