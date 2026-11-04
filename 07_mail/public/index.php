<?php
session_start();

$name  = isset($_SESSION['name']) ? $_SESSION['name'] : '';
$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$body  = isset($_SESSION['body']) ? $_SESSION['body'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>お問い合わせフォーム</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex items-center justify-center px-4 py-10">
    <!-- ローディングオーバーレイ -->
    <div id="loadingOverlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/70 backdrop-blur-sm">
        <div class="flex flex-col items-center">
            <svg class="animate-spin h-12 w-12 text-white mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <p class="text-white text-lg font-semibold">送信中...</p>
        </div>
    </div>

    <main class="w-full max-w-xl">
        <div class="text-center mb-8">
            <span class="inline-grid place-items-center w-12 h-12 rounded-2xl bg-indigo-600 text-white shadow-sm mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </span>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">お問い合わせ</h1>
            <p class="text-sm text-slate-500 mt-2">以下のフォームに入力してください。内容を確認のうえご連絡いたします。</p>
        </div>

        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sm:p-8">
            <?php if ($error): ?>
                <div class="mb-6 flex items-start gap-2 px-4 py-3 rounded-xl border border-red-200 bg-red-50 text-sm text-red-700" role="alert">
                    <span>⚠️</span>
                    <span><?= $h($error) ?></span>
                </div>
            <?php endif ?>

            <form action="send.php" method="POST" class="space-y-5" onsubmit="handleSubmit(event)">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">お名前 <span class="text-red-500">*</span></label>
                    <input id="name" type="text" name="name" value="<?= $h($name) ?>" required placeholder="山田 太郎"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">メールアドレス <span class="text-red-500">*</span></label>
                    <input id="email" type="email" name="email" value="<?= $h($email) ?>" required placeholder="you@example.com"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label for="body" class="block text-sm font-medium text-slate-700 mb-1.5">お問い合わせ内容 <span class="text-red-500">*</span></label>
                    <textarea id="body" name="body" rows="6" required placeholder="お問い合わせ内容をご記入ください"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition"><?= $h($body) ?></textarea>
                </div>
                <button type="submit"
                    class="w-full py-3 text-sm font-semibold text-white rounded-full bg-indigo-600 hover:bg-indigo-700 transition">
                    送信する
                </button>
            </form>
        </div>
    </main>

    <script>
        function handleSubmit(event) {
            const overlay = document.getElementById("loadingOverlay");
            overlay.classList.remove("hidden");
            overlay.classList.add("flex");
        }
    </script>
</body>

</html>
