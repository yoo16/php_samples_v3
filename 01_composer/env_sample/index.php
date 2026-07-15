<?php
// 共通の初期化ファイルを読み込む（Composer オートローダーなど）
require_once dirname(__DIR__, 2) . '/bootstrap.php';

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
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Load Env</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-sky-600 px-6 py-5">
                <h1 class="text-2xl font-bold text-white">Environment Variables</h1>
                <p class="text-sky-100 text-sm mt-1">.env から読み込んだ設定値</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- API Information -->
                <section>
                    <h2 class="flex items-center gap-2 text-sm font-semibold text-slate-500 uppercase tracking-wide mb-3">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        API Information
                    </h2>
                    <dl class="rounded-xl border border-slate-200 divide-y divide-slate-100">
                        <div class="flex items-center justify-between px-4 py-3">
                            <dt class="text-slate-600 font-medium">API Key</dt>
                            <dd class="font-mono text-sm text-slate-800 bg-slate-100 rounded px-2 py-1"><?= htmlspecialchars(API_KEY) ?></dd>
                        </div>
                    </dl>
                </section>

                <!-- Database Information -->
                <section>
                    <h2 class="flex items-center gap-2 text-sm font-semibold text-slate-500 uppercase tracking-wide mb-3">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        Database Information
                    </h2>
                    <dl class="rounded-xl border border-slate-200 divide-y divide-slate-100">
                        <div class="flex items-center justify-between px-4 py-3">
                            <dt class="text-slate-600 font-medium">Host</dt>
                            <dd class="font-mono text-sm text-slate-800"><?= htmlspecialchars(DB_HOST) ?></dd>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3">
                            <dt class="text-slate-600 font-medium">Name</dt>
                            <dd class="font-mono text-sm text-slate-800"><?= htmlspecialchars(DB_NAME) ?></dd>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3">
                            <dt class="text-slate-600 font-medium">User</dt>
                            <dd class="font-mono text-sm text-slate-800"><?= htmlspecialchars(DB_USER) ?></dd>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3">
                            <dt class="text-slate-600 font-medium">Password</dt>
                            <dd class="font-mono text-sm text-slate-800 bg-slate-100 rounded px-2 py-1"><?= htmlspecialchars(DB_PASSWORD) ?></dd>
                        </div>
                    </dl>
                </section>
            </div>
        </div>
    </main>
</body>

</html>