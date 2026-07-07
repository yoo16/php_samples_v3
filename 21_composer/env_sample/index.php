<?php
// Composer のオートローダーを読み込む
require_once __DIR__ . '/../../vendor/autoload.php';

// 環境変数を読み込むための Dotenv ライブラリを使用
use Dotenv\Dotenv;

// .env ファイルから環境変数を読み込む
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 環境変数を定数として定義
define('API_KEY', $_ENV['API_KEY']);
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('DB_NAME', $_ENV['DB_NAME']);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Load Env</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="mx-auto flex min-h-screen w-full max-w-5xl flex-col justify-center px-5 py-10 sm:px-8">
        <header class="mb-8">
            <p class="mb-2 text-sm font-semibold uppercase tracking-wide text-emerald-700">Composer dotenv sample</p>
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">Environment Variable Example</h1>
            <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
                Values loaded from the local <code class="rounded bg-white px-1.5 py-0.5 text-sm text-slate-800">.env</code> file are displayed below.
            </p>
        </header>

        <div class="grid gap-5 lg:grid-cols-[0.85fr_1.15fr]">
            <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold">API Information</h2>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Loaded</span>
                </div>

                <dl class="space-y-3">
                    <div class="rounded-md bg-slate-50 p-4">
                        <dt class="text-sm font-medium text-slate-500">API Key</dt>
                        <dd class="mt-1 break-all font-mono text-sm text-slate-900"><?= e(API_KEY) ?></dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold">Database Information</h2>
                    <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-800">Configured</span>
                </div>

                <dl class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-md bg-slate-50 p-4">
                        <dt class="text-sm font-medium text-slate-500">DB Host</dt>
                        <dd class="mt-1 break-all font-mono text-sm text-slate-900"><?= e(DB_HOST) ?></dd>
                    </div>
                    <div class="rounded-md bg-slate-50 p-4">
                        <dt class="text-sm font-medium text-slate-500">Database Name</dt>
                        <dd class="mt-1 break-all font-mono text-sm text-slate-900"><?= e(DB_NAME) ?></dd>
                    </div>
                    <div class="rounded-md bg-slate-50 p-4">
                        <dt class="text-sm font-medium text-slate-500">User</dt>
                        <dd class="mt-1 break-all font-mono text-sm text-slate-900"><?= e(DB_USER) ?></dd>
                    </div>
                    <div class="rounded-md bg-slate-50 p-4">
                        <dt class="text-sm font-medium text-slate-500">Password</dt>
                        <dd class="mt-1 break-all font-mono text-sm text-slate-900"><?= e(DB_PASSWORD) ?></dd>
                    </div>
                </dl>
            </section>
        </div>
    </main>
</body>

</html>
