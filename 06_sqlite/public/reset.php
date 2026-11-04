<?php
// ============================================================
// reset.php - DB初期化
// notices テーブルを削除して作り直し、アップロード画像も削除する
// ============================================================
require_once __DIR__ . "/../src/db.php";

// GET で開いた場合は誤操作防止のため何もしない
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// テーブルを削除して再作成
$sql = "DROP TABLE IF EXISTS notices";
$pdo->exec($sql);
createTables();

// アップロード画像を削除
foreach (glob(__DIR__ . "/uploads/*") as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}

// 完了メッセージ付きでトップへ戻す
header("Location: index.php?reset=1");
exit;
