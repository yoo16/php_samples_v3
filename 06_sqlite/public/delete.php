<?php
// ============================================================
// delete.php - 投稿削除（POST のみ）
// 該当レコードと、紐づくアップロード画像を削除する
// ============================================================
require_once __DIR__ . "/../src/db.php";

if ($pdo === null) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// POST で送信された id を取得
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    header("Location: index.php");
    exit;
}

// 削除対象のレコードの画像ファイル名を取得
$sql = "SELECT image FROM notices WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
// 画像ファイル名を取得
$image = $stmt->fetchColumn();

// レコード削除SQL
$sql = "DELETE FROM notices WHERE id = ?";
$pdo->prepare($sql)->execute([$id]);

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
