<?php
// ============================================================
// delete.php - 投稿削除（POST のみ）
// 該当レコードと、紐づくアップロード画像を削除する
// ============================================================
require_once __DIR__ . "/../src/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT image FROM notices WHERE id = ?");
$stmt->execute([$id]);
$image = $stmt->fetchColumn();

$pdo->prepare("DELETE FROM notices WHERE id = ?")->execute([$id]);

// 画像ファイルを削除（uploads/ 配下のみ）
if (is_string($image) && $image !== "") {
    $uploadDir = realpath(__DIR__ . "/uploads");
    $file = realpath(__DIR__ . "/" . $image);
    if ($uploadDir !== false && $file !== false && str_starts_with($file, $uploadDir . DIRECTORY_SEPARATOR) && is_file($file)) {
        unlink($file);
    }
}

header("Location: index.php?deleted=1");
exit;
