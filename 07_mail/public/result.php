<?php
session_start();

// セッションデータをクリア
if (isset($_SESSION['name'])) unset($_SESSION['name']);
if (isset($_SESSION['email'])) unset($_SESSION['email']);
if (isset($_SESSION['body'])) unset($_SESSION['body']);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>お問い合わせ完了</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex items-center justify-center px-4 py-10">
    <main class="w-full max-w-xl bg-white border border-slate-200 shadow-sm rounded-2xl p-8 sm:p-10 text-center">
        <div class="mx-auto mb-6 w-16 h-16 grid place-items-center rounded-full bg-emerald-100">
            <svg class="w-9 h-9 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">お問い合わせを受け付けました</h1>
        <p class="text-slate-500 leading-relaxed mb-8">
            担当者が内容を確認し、必要に応じてご連絡いたします。<br>
            しばらくお待ちください。
        </p>

        <a href="./" class="inline-block px-6 py-2.5 text-sm font-semibold text-white rounded-full bg-indigo-600 hover:bg-indigo-700 transition">
            フォームに戻る
        </a>
    </main>
</body>

</html>
