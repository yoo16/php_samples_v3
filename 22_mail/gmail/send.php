<?php
session_start();
require './Contact.php';

$name  = $_POST['name']  ?? '';
$email = $_POST['email'] ?? '';
$body  = $_POST['body']  ?? '';

$_SESSION['name']  = $name;
$_SESSION['email'] = $email;
$_SESSION['body']  = $body;

$contact = new Contact();
// バリデーション
$error = $contact->validate($name, $email, $body);
if ($error) {
    $_SESSION['error'] = $error;
    header("Location: ./");
    exit;
}

// メール送信
$result = false;
$result = $contact->send($name, $email, $body);
if ($result === true) {
    $_SESSION['success'] = "お問い合わせを送信しました。";
    header("Location: result.php");
} else {
    $_SESSION['error'] = "送信に失敗しました: {$result}";
    header("Location: ./");
}
exit;