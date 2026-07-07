<?php
require __DIR__ . '/../vendor/autoload.php';

use League\OAuth2\Client\Provider\Google;
use Dotenv\Dotenv;

session_start();

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$provider = new Google([
    'clientId'     => $_ENV['GOOGLE_CLIENT_ID'],
    'clientSecret' => $_ENV['GOOGLE_CLIENT_SECRET'],
    'redirectUri'  => $_ENV['CALLBACK_URL'],
]);

// 状態初期化
$errorMsg = null;
$user = null;

// 1. CSRF検証
if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    $errorMsg = '不正なセッション（State不一致）です。最初からやり直してください。';
} else {
    try {
        // 2. トークン取得
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // 3. ユーザー情報取得
        $user = $provider->getResourceOwner($token);
    } catch (\Exception $e) {
        $errorMsg = 'Google認証中にエラーが発生しました: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログインステータス</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        
        <?php if ($errorMsg): ?>
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-50 text-red-500 mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mb-2">ログイン失敗</h2>
                <p class="text-sm text-slate-500 mb-6"><?php echo htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8'); ?></p>
                <a href="/" class="inline-flex w-full justify-center rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-700 transition">
                    トップへ戻る
                </a>
            </div>

        <?php else: ?>
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-50 text-green-500 mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mb-1">ログイン成功</h2>
                <p class="text-sm text-green-600 font-medium mb-6">認証が完了しました</p>
                
                <div class="flex items-center space-x-4 bg-slate-50 p-4 rounded-xl border border-slate-100 text-left mb-6">
                    <img class="h-12 w-12 rounded-full ring-2 ring-white shadow-sm" src="<?php echo htmlspecialchars($user->getAvatar(), ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate"><?php echo htmlspecialchars($user->getName(), ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-xs text-slate-500 truncate"><?php echo htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>

                <a href="/dashboard" class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 transition">
                    ダッシュボードへ進む
                </a>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>