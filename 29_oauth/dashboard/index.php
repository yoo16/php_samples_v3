<?php
session_start();

$user = $_SESSION['user'] ?? null;

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto w-full max-w-6xl px-5 py-8">
        <?php if (!$user): ?>
            <section class="mx-auto mt-20 max-w-md rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto mb-5 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">ログインが必要です</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    ダッシュボードを表示するには、Googleアカウントでログインしてください。
                </p>
                <a href="../login.php" class="mt-6 inline-flex w-full justify-center rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                    ログインする
                </a>
            </section>
        <?php else: ?>
            <header class="mb-8 flex flex-col gap-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= e($user['avatar']) ?>" alt="" class="h-14 w-14 rounded-full border border-slate-200 object-cover">
                    <?php else: ?>
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-xl font-bold text-blue-700">
                            U
                        </div>
                    <?php endif; ?>
                    <div>
                        <p class="text-sm font-semibold text-blue-700">OAuth Dashboard</p>
                        <h1 class="text-2xl font-bold">ようこそ、<?= e($user['name'] ?? 'ユーザー') ?> さん</h1>
                        <p class="mt-1 text-sm text-slate-500"><?= e($user['email'] ?? '') ?></p>
                    </div>
                </div>

                <a href="../login.php" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    別のアカウントでログイン
                </a>
            </header>

            <section class="grid gap-5 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">ログイン状態</p>
                    <p class="mt-3 text-2xl font-bold text-emerald-600">認証済み</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Google OAuthでユーザー情報を取得できています。</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">表示名</p>
                    <p class="mt-3 break-words text-2xl font-bold"><?= e($user['name'] ?? '-') ?></p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Googleアカウントに登録されている名前です。</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">メールアドレス</p>
                    <p class="mt-3 break-words text-lg font-bold"><?= e($user['email'] ?? '-') ?></p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">認証後に取得したメールアドレスです。</p>
                </div>
            </section>

            <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold">取得したユーザー情報</h2>
                <dl class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="rounded-lg bg-slate-50 p-4">
                        <dt class="text-sm font-semibold text-slate-500">Name</dt>
                        <dd class="mt-1 break-words font-mono text-sm"><?= e($user['name'] ?? '') ?></dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4">
                        <dt class="text-sm font-semibold text-slate-500">Email</dt>
                        <dd class="mt-1 break-words font-mono text-sm"><?= e($user['email'] ?? '') ?></dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-4 md:col-span-2">
                        <dt class="text-sm font-semibold text-slate-500">Avatar URL</dt>
                        <dd class="mt-1 break-all font-mono text-sm"><?= e($user['avatar'] ?? '') ?></dd>
                    </div>
                </dl>
            </section>
        <?php endif; ?>
    </main>
</body>

</html>
