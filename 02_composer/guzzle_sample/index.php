<?php
// Composer のオートローダーを読み込む
require_once dirname(__DIR__, 2) . '/bootstrap.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$user = null;   // 取得したユーザー情報
$error = null;  // エラーメッセージ

// フォーム送信時に GitHub API へリクエスト
$username = trim($_GET['username'] ?? 'octocat');

if ($username !== '') {
    // Guzzle クライアントを生成
    $client = new Client([
        'base_uri' => 'https://api.github.com/',
        'timeout'  => 5.0,
    ]);

    try {
        // GET https://api.github.com/users/{username}
        $response = $client->get("users/{$username}", [
            'headers' => ['Accept' => 'application/vnd.github+json'],
        ]);

        // レスポンスボディ(JSON)を連想配列にデコード
        $user = json_decode($response->getBody()->getContents(), true);
    } catch (GuzzleException $e) {
        // 404 やネットワークエラーなどをキャッチ
        $error = "ユーザーを取得できませんでした（{$e->getCode()}）";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guzzle HTTP Client</title>
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-sky-600 px-6 py-5">
                <h1 class="text-2xl font-bold text-white">Guzzle HTTP Client</h1>
                <p class="text-sky-100 text-sm mt-1">GitHub API からユーザー情報を取得</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- 検索フォーム -->
                <form method="get" class="flex gap-2">
                    <input
                        type="text"
                        name="username"
                        value="<?= htmlspecialchars($username) ?>"
                        placeholder="GitHub ユーザー名"
                        class="flex-1 rounded-xl border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <button
                        type="submit"
                        class="rounded-xl bg-sky-600 px-5 py-2 font-semibold text-white hover:bg-sky-700 transition">
                        取得
                    </button>
                </form>

                <?php if ($error): ?>
                    <!-- エラー表示 -->
                    <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php elseif ($user): ?>
                    <!-- プロフィールカード -->
                    <section class="flex items-center gap-4 rounded-xl border border-slate-200 p-4">
                        <img
                            src="<?= htmlspecialchars($user['avatar_url']) ?>"
                            alt="avatar"
                            class="w-16 h-16 rounded-full">
                        <div>
                            <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($user['name'] ?? $user['login']) ?></p>
                            <a href="<?= htmlspecialchars($user['html_url']) ?>" target="_blank"
                                class="text-sm text-sky-600 hover:underline">@<?= htmlspecialchars($user['login']) ?></a>
                            <?php if (!empty($user['bio'])): ?>
                                <p class="text-sm text-slate-500 mt-1"><?= htmlspecialchars($user['bio']) ?></p>
                            <?php endif; ?>
                        </div>
                    </section>

                    <!-- 統計情報 -->
                    <dl class="grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-xl border border-slate-200 py-3">
                            <dt class="text-xs text-slate-500 uppercase tracking-wide">Repos</dt>
                            <dd class="text-xl font-bold text-slate-800"><?= (int) $user['public_repos'] ?></dd>
                        </div>
                        <div class="rounded-xl border border-slate-200 py-3">
                            <dt class="text-xs text-slate-500 uppercase tracking-wide">Followers</dt>
                            <dd class="text-xl font-bold text-slate-800"><?= (int) $user['followers'] ?></dd>
                        </div>
                        <div class="rounded-xl border border-slate-200 py-3">
                            <dt class="text-xs text-slate-500 uppercase tracking-wide">Following</dt>
                            <dd class="text-xl font-bold text-slate-800"><?= (int) $user['following'] ?></dd>
                        </div>
                    </dl>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>
