<?php
require_once __DIR__ . "/../src/db.php";

// 最新 20 件を取得
$sql = "SELECT * FROM notices ORDER BY created_at DESC LIMIT 20";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>MyPix - 画像投稿サイト</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com?plugins=line-clamp"></script>
    <script defer src="js/modal.js"></script>
</head>

<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col">
    <?php include __DIR__ . "/../src/components/header.php"; ?>

    <main class="flex-1 w-full max-w-6xl mx-auto mt-10 px-4">
        <div class="flex items-end justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">最新の投稿</h1>
                <p class="text-sm text-slate-500 mt-1"><?= count($posts) ?> 件の写真</p>
            </div>
        </div>

        <?php if (isset($_GET['reset'])): ?>
            <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">
                ✅ DBを初期化しました。
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">
                🗑️ 投稿を削除しました。
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['posted'])): ?>
            <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">
                ✅ 投稿が完了しました！
            </div>
        <?php endif; ?>

        <?php if (count($posts) === 0): ?>
            <div class="py-20 px-6 bg-white border border-dashed border-slate-300 rounded-2xl text-center">
                <div class="text-5xl mb-3">📷</div>
                <p class="text-slate-500 mb-5">まだ投稿はありません。最初の写真をアップロードしてみましょう！</p>
                <a href="post.php" class="inline-block px-5 py-2.5 text-sm font-semibold text-white rounded-full bg-indigo-600 hover:bg-indigo-700 shadow transition">投稿する</a>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($posts as $post): ?>
                <article class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 overflow-hidden cursor-pointer"
                    data-id="<?= (int)$post['id'] ?>"
                    data-image="<?= htmlspecialchars($post['image'] ?? '') ?>"
                    data-title="<?= htmlspecialchars($post['title']) ?>"
                    data-body="<?= htmlspecialchars($post['body'] ?? '') ?>"
                    data-date="<?= date("Y年m月d日 H:i", strtotime($post['created_at'])) ?>"
                    onclick="openModal(this)">
                    <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>"
                                loading="lazy"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <?php else: ?>
                            <div class="w-full h-full grid place-items-center text-slate-300 text-sm">No Image</div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-slate-900 truncate"><?= htmlspecialchars($post['title']) ?></h3>
                        <time class="block text-xs text-slate-400 mt-0.5">
                            <?= date("Y年m月d日 H:i", strtotime($post['created_at'])) ?>
                        </time>
                        <?php if (!empty($post['body'])): ?>
                            <p class="text-sm text-slate-600 mt-2 line-clamp-2"><?= htmlspecialchars($post['body']) ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- モーダル -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4"
        onclick="if (event.target === this) closeModal()">
        <div class="relative w-full max-w-4xl max-h-full overflow-y-auto bg-white rounded-2xl shadow-2xl">
            <button onclick="closeModal()" aria-label="閉じる"
                class="absolute top-3 right-3 w-9 h-9 grid place-items-center rounded-full bg-black/50 text-white hover:bg-black/70 transition">✕</button>
            <img id="modalImage" src="" alt="拡大画像" class="w-full max-h-[70vh] object-contain bg-slate-900">
            <div class="p-6">
                <h3 id="modalTitle" class="text-xl font-bold text-slate-900"></h3>
                <p id="modalDate" class="text-xs text-slate-400 mt-1"></p>
                <p id="modalBody" class="mt-3 text-slate-600 whitespace-pre-line"></p>
                <form action="delete.php" method="post" class="mt-6 pt-4 border-t border-slate-100 text-right"
                    onsubmit="return confirm('この投稿を削除します。よろしいですか？')">
                    <input type="hidden" name="id" id="modalId">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 rounded-full hover:bg-red-50 transition">🗑️ 削除</button>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . "/../src/components/footer.php"; ?>
</body>

</html>
