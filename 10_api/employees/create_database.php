<?php
require_once 'env.php';

session_start();

// CSRFトークンの生成
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";
$status = null;

// SQLファイルの読み込み（プレビュー用）
$schema_sql = file_exists("docs/schema.sql") ? file_get_contents("docs/schema.sql") : "-- Schema file not found";
$insert_sql = file_exists("docs/insert_data.sql") ? file_get_contents("docs/insert_data.sql") : "-- Insert data file not found";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRFトークンの検証
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('不正なリクエストです。');
    }

    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        // 1. データベース作成
        $sql = "CREATE DATABASE IF NOT EXISTS " . DB_DATABASE;
        $pdo->exec($sql);

        // 2. データベース選択
        $sql = "USE " . DB_DATABASE;
        $pdo->exec($sql);

        // 3. スキーマ作成
        $pdo->exec($schema_sql);
        $message .= "✅ データベーススキーマを作成しました。" . PHP_EOL;

        // 4. 初期データ挿入
        $pdo->exec($insert_sql);
        $message .= "✅ 初期データの挿入が完了しました。" . PHP_EOL;

        $status = "success";
    } catch (Exception $e) {
        $status = "error";
        $message = "❌ エラーが発生しました: " . $e->getMessage();
    }
}

$title = 'データベース初期化';
$lesson_number = 10;
$description = 'MySQL データベースの作成、テーブルの定義（スキーマ）、および初期データの投入を一括で行います。';
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> | PHP Samples</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/app.css">
</head>

<body class="bg-slate-50 text-slate-800 leading-relaxed antialiased">

    <?php include 'components/nav.php'; ?>

    <main class="max-w-4xl mx-auto px-6 py-12">

        <header class="mb-12 text-center md:text-left">
            <div class="inline-block px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-4">
                Lesson <?= $lesson_number ?>
            </div>
            <h2 class="text-4xl font-extrabold text-slate-900 mb-4 tracking-tight"><?= $title ?></h2>
            <p class="text-lg text-slate-600 max-w-2xl"><?= $description ?></p>
        </header>

        <?php if ($status === 'success'): ?>
            <div class="mb-12 p-6 bg-emerald-50 border border-emerald-200 rounded-2xl animate-in fade-in zoom-in duration-300">
                <div class="flex items-center gap-3 mb-4 text-emerald-700">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-bold">セットアップ完了</h3>
                </div>
                <div class="bg-white/50 p-4 rounded-xl font-mono text-sm text-emerald-800 leading-loose mb-6">
                    <?= nl2br(htmlspecialchars($message)) ?>
                </div>
                <a href="select_users.php" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-200">
                    ユーザ一覧を確認する
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        <?php elseif ($status === 'error'): ?>
            <div class="mb-12 p-6 bg-rose-50 border border-rose-200 rounded-2xl">
                <div class="flex items-center gap-3 mb-4 text-rose-700">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-bold">セットアップ失敗</h3>
                </div>
                <p class="text-rose-800 font-medium"><?= nl2br(htmlspecialchars($message)) ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: SQL Previews -->
            <div class="lg:col-span-2 space-y-6">
                <section class="bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-800">
                    <div class="px-6 py-4 bg-slate-800 flex items-center justify-between">
                        <h3 class="text-slate-300 text-xs font-bold tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3H18c1 0 2-1 2-2V7c0-2-1.5-3-3.5-3H7c-2 0-3 1-3 3z"></path>
                            </svg>
                            スキーマ SQL (docs/schema.sql)
                        </h3>
                    </div>
                    <div class="p-6 sql-preview font-mono text-[11px] text-indigo-300 leading-relaxed bg-slate-950">
                        <pre><code><?= htmlspecialchars($schema_sql) ?></code></pre>
                    </div>
                </section>

                <section class="bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-800">
                    <div class="px-6 py-4 bg-slate-800 flex items-center justify-between">
                        <h3 class="text-slate-300 text-xs font-bold tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            データ SQL (docs/insert_data.sql)
                        </h3>
                    </div>
                    <div class="p-6 sql-preview font-mono text-[11px] text-emerald-300 leading-relaxed bg-slate-950">
                        <pre><code><?= htmlspecialchars($insert_sql) ?></code></pre>
                    </div>
                </section>
            </div>

            <!-- Right: Action Card -->
            <div class="lg:col-span-1">
                <section class="bg-white rounded-3xl border border-slate-200 shadow-xl sticky top-24">
                    <div class="p-6 bg-amber-50 border-b border-amber-100">
                        <h3 class="text-amber-900 font-bold flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            実行前の注意
                        </h3>
                        <p class="text-amber-800 text-[11px] leading-relaxed">
                            この操作を実行すると、データベース <strong><?= DB_DATABASE ?></strong> が初期化されます。現在のデータは全て削除されます。
                        </p>
                    </div>

                    <form action="" method="post" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="space-y-4">
                            <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">実行プロセス</h4>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 text-xs text-slate-600">
                                    <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center font-bold text-[10px]">1</div>
                                    <span>データベース作成</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-slate-600">
                                    <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center font-bold text-[10px]">2</div>
                                    <span>テーブル作成</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-slate-600">
                                    <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center font-bold text-[10px]">3</div>
                                    <span>テストデータ挿入</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl transition shadow-lg shadow-indigo-200 flex items-center justify-center gap-2 group active:scale-95">
                            <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            セットアップを実行
                        </button>
                    </form>
                </section>
            </div>
        </div>

        <footer class="pt-12 mt-20 border-t border-slate-200 text-center">
            <p class="text-slate-500 text-sm italic">"SQLの内容を理解してから実行することは、安全な開発の第一歩です。"</p>
        </footer>
    </main>

</body>

</html>