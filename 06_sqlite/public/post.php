<?php
require_once __DIR__ . "/../src/db.php";

$message = "";

// UUID v4 を生成（例: 3f2b8c1e-9a47-4d0b-8e65-1c2a7f4b9d10）
function uuidv4(): string
{
    $b = random_bytes(16);
    $b[6] = chr((ord($b[6]) & 0x0f) | 0x40); // バージョン 4
    $b[8] = chr((ord($b[8]) & 0x3f) | 0x80); // バリアント
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
}

// アップロード用ディレクトリ確認
$uploadDir = __DIR__ . "/uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    chmod($uploadDir, 0777);
}

// 投稿処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title'] ?? "");
    $body  = trim($_POST['body'] ?? "");
    $imagePath = null;

    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (in_array($ext, $allowed)) {
            // 元のファイル名は使わず、UUID でユニークな名前にする
            $fileName = uuidv4() . "." . $ext;
            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = "uploads/" . $fileName;
            } else {
                $message = "⚠️ 画像アップロードに失敗しました。";
            }
        } else {
            $message = "⚠️ 許可されていないファイル形式です。";
        }
    }

    if ($title !== "" || $imagePath !== null) { // タイトル or 画像があれば投稿可能
        $sql = "INSERT INTO notices (title, body, image) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $body, $imagePath]);
        $message = "✅ 投稿が完了しました！";
        header("Location: index.php?posted=1");
        exit;
    } else {
        $message = "⚠️ タイトルか画像のどちらかは必須です。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>投稿フォーム | MyPix</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com?plugins=line-clamp"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col">
    <?php include __DIR__ . "/../src/components/header.php"; ?>

    <main class="flex-1 w-full max-w-2xl mx-auto mt-10 px-4">
        <?php if ($message): ?>
            <div class="mb-6 px-4 py-3 rounded-xl text-sm border <?= str_contains($message, "✅") ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-6 sm:p-8">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">新規投稿</h1>
            <p class="text-sm text-slate-500 mt-1 mb-6">タイトルか画像のどちらかは必須です。</p>

            <form method="post" enctype="multipart/form-data" class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-1.5">タイトル</label>
                    <input id="title" type="text" name="title" placeholder="例：夕焼けの海"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label for="body" class="block text-sm font-medium text-slate-700 mb-1.5">本文</label>
                    <textarea id="body" name="body" rows="4" placeholder="写真についてひとこと"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">画像</label>
                    <label for="image" class="flex flex-col items-center justify-center gap-1 px-4 py-8 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 text-slate-500 hover:border-indigo-400 hover:bg-indigo-50/50 cursor-pointer transition">
                        <span class="text-3xl">🖼️</span>
                        <span id="fileName" class="text-sm">クリックして画像を選択（jpg / png / gif）</span>
                    </label>
                    <input id="image" type="file" name="image" accept="image/*" class="sr-only"
                        onchange="document.getElementById('fileName').textContent = this.files[0]?.name ?? 'クリックして画像を選択（jpg / png / gif）'">
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="index.php" class="px-4 py-2.5 text-sm font-medium text-slate-500 rounded-full hover:bg-slate-100 transition">キャンセル</a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white rounded-full bg-indigo-600 hover:bg-indigo-700 shadow-sm hover:shadow-md transition">
                        投稿する
                    </button>
                </div>
            </form>
        </div>
    </main>

    <?php include __DIR__ . "/../src/components/footer.php"; ?>
</body>
</html>
