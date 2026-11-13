## アプリの概要

`PHPMailer` と `Gmail` の `SMTP` サーバーを利用して、お問い合わせフォームから**確認メールを送信**するアプリを作成します。この章では、まず**メール送信の基本**（仕組み、`SMTP`、`TLS`、`Gmail` の送信設定）を学びます。

| 機能 | ファイル | 内容 |
| ---- | ---- | ---- |
| フォーム表示 | public/index.php | お名前・メールアドレス・内容の入力 |
| 送信処理 | public/send.php | 入力チェックとメール送信 |
| 完了表示 | public/result.php | 送信完了の画面 |
| メール送信 | src/Contact.php | `PHPMailer` による送信処理 |
| メール本文 | src/templates/contact_mail.html | `HTML` メールのひな形 |
| 接続情報 | .env | `SMTP` サーバーやパスワードの設定 |

### ファイル構成

```txt
07_mail/
├── public/            ← 公開フォルダ（-t public）
│   ├── index.php
│   ├── send.php
│   └── result.php
├── src/               ← 公開しないプログラム
│   ├── Contact.php
│   └── templates/
│       └── contact_mail.html
├── vendor/            ← Composer が入れるライブラリ
├── .env               ← 接続情報（Git 管理しない）
├── .env.sample        ← .env の見本（Git 管理する）
└── composer.json
```

> `.env` には**パスワードが入ります。** `.gitignore` に追加して、Git に登録されないようにします。`public` の外に置く理由は、06 章の「公開フォルダを分ける理由」と同じです。

---

## メール送信の仕組み

### PHPからメールが届くまで

`PHP` からメールを送るとき、`PHP` が相手のメールボックスへ直接届けるわけではありません。**メールサーバーに配送を依頼**します。

```txt
PHP アプリ ──→ 送信サーバー ──→ 相手のサーバー ──→ 受信者
              (smtp.gmail.com)  (受信側の SMTP)    (メールソフト)
```

| 登場するもの | 役割 |
| ---- | ---- |
| PHP アプリ | メールの宛先と本文を作り、送信サーバーに渡す |
| 送信サーバー | 受け取ったメールを、宛先のサーバーへ配送する |
| 受信サーバー | メールを保管し、受信者が読めるようにする |

> このアプリが話す相手は、**最初の送信サーバーだけ**です。その先の配送は、送信サーバーが引き受けます。

### SMTPとは

**SMTP**（Simple Mail Transfer Protocol）は、メールを**送る**ための通信ルールです。ブラウザと Web サーバーが `HTTP` で話すように、メールの送信は `SMTP` で話します。

| プロトコル | 用途 |
| ---- | ---- |
| SMTP | メールを**送信**する |
| POP3 / IMAP | メールを**受信**する（メールソフトが使う） |

`SMTP` は、次のようなやり取りで進みます。実際の通信は文字のやり取りです。

```txt
C: EHLO localhost                        ← 挨拶
S: 250-smtp.gmail.com at your service
C: STARTTLS                              ← 暗号化を開始（後述）
S: 220 2.0.0 Ready to start TLS
C: AUTH LOGIN ...                        ← ログイン
S: 235 2.7.0 Accepted
C: MAIL FROM:<from@example.com>          ← 差出人
S: 250 2.1.0 OK
C: RCPT TO:<to@example.com>              ← 宛先
S: 250 2.1.5 OK
C: DATA                                  ← 本文の送信
S: 354 Go ahead
C: Subject: ...  (本文)  .
S: 250 2.0.0 OK
C: QUIT
```

`C` はクライアント（`PHP`）、`S` はサーバーです。`PHPMailer` が、この手順をすべて代わりに行います。

### SMTPの設定項目

`SMTP` で送るには、次の情報が必要です。

| 項目 | 例（Gmail） | 内容 |
| ---- | ---- | ---- |
| Host | smtp.gmail.com | 送信サーバーの名前 |
| Port | 587 | 接続する番号 |
| 暗号化 | tls（STARTTLS） | 通信の暗号化方式 |
| Username | あなたの Gmail アドレス | ログインに使う ID |
| Password | アプリパスワード | ログインに使うパスワード |

---

## TLSとは

### なぜ暗号化が必要か

`SMTP` は、もともと**暗号化されていない**文字のやり取りです。そのまま使うと、途中の経路で次の内容が読み取られる可能性があります。

| 流れる情報 | 読まれたときの被害 |
| ---- | ---- |
| ログインの ID とパスワード | アカウントが乗っ取られる |
| メールの宛先・本文 | 個人情報や内容が漏れる |

**TLS**（Transport Layer Security）は、通信を暗号化する仕組みです。`https://` の `s` と同じ技術で、第三者に盗み見られても中身が分からなくなります。

> 古い名前の `SSL` が今も使われることがあります。現在使われているのは `TLS` で、設定画面などで「SSL/TLS」と書かれていても、実際には `TLS` を指します。

### STARTTLS と SMTPS

`SMTP` で `TLS` を使う方法は2通りあります。ポート番号で決まります。

| 方式 | ポート | PHPMailer の指定 | 内容 |
| ---- | ---- | ---- | ---- |
| STARTTLS | **587** | `ENCRYPTION_STARTTLS`（`'tls'`） | 平文で接続し、途中から暗号化に切り替える |
| SMTPS | 465 | `ENCRYPTION_SMTPS`（`'ssl'`） | 最初から暗号化して接続する |
| 暗号化なし | 25 | 指定なし | 暗号化されない（使わない） |

このサンプルは、`Gmail` の推奨である **ポート 587 + STARTTLS** を使います。

> ポートと暗号化の組み合わせを間違えると、接続が止まったり、エラーになったりします。**587 なら `tls`、465 なら `ssl`** と、セットで覚えます。

---

## Gmailで送信する準備

`Gmail` の `SMTP` サーバーを使うと、自分の `Gmail` アドレスから送信できます。**普段のログインパスワードは使えません。** 代わりに**アプリパスワード**を発行して使います。

### アプリパスワードとは

特定のアプリだけが使える、**16 桁の専用パスワード**です。

| 項目 | ログインパスワード | アプリパスワード |
| ---- | ---- | ---- |
| 用途 | Google アカウントへのログイン | 特定のアプリからの接続 |
| 漏れたとき | アカウント全体が危険 | そのパスワードだけを削除すれば止められる |
| 発行の条件 | なし | **2段階認証がオン** |

> 以前あった「安全性の低いアプリの許可」の設定は廃止されました。現在は、**2段階認証 + アプリパスワード**が `Gmail` の `SMTP` を使う方法です。

### 発行の手順

1. `Google アカウント`（`https://myaccount.google.com/security`）を開いてログインする
2. 「セキュリティ」を開き、「**2段階認証プロセス**」をオンにする
3. `https://myaccount.google.com/apppasswords` の「**アプリパスワード**」を開く
4. 任意の**アプリ名**（例：`php_mail`）を入力して「作成」を押す
5. 表示された **16 桁のパスワード**をコピーして保存する

> パスワードが表示されるのは**一度だけ**です。画面を閉じると見られなくなるため、必ず控えてから閉じます。忘れた場合は、削除して作り直します。

> 「アプリパスワード」の項目が見つからない場合は、2段階認証がオンになっていないか、組織（会社・学校）のアカウントで管理者が制限している可能性があります。URL を直接開くと表示される場合があります。

### Gmailの接続情報

| 項目 | 設定値 |
| ---- | ---- |
| Host | smtp.gmail.com |
| Port | 587 |
| 暗号化 | tls |
| Username | Gmail アドレス（`xxxx@gmail.com`） |
| Password | 発行した 16 桁のアプリパスワード |

---

## 環境変数（.env）

接続情報は、プログラムに直接書かず `.env` に分けます。パスワードをコードに書くと、`Git` に登録された時点で**公開されたのと同じ**になるためです。

### .env を作る

`.env.sample` をコピーして `.env` を作り、自分の値に書き換えます。

```bash
cp .env.sample .env
```

```ini
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_FROM_ADDRESS=your@gmail.com
MAIL_FROM_NAME="Your Name"
```

| 項目 | 内容 |
| ---- | ---- |
| MAIL_HOST / MAIL_PORT | 送信サーバーとポート |
| MAIL_ENCRYPTION | `tls`（587）または `ssl`（465） |
| MAIL_USERNAME | ログインに使うアドレス |
| MAIL_PASSWORD | **アプリパスワード**（空白は入れない） |
| MAIL_FROM_ADDRESS | 差出人のアドレス |
| MAIL_FROM_NAME | 差出人の表示名（空白を含むときは `"` で囲む） |

> `Gmail` では、`MAIL_FROM_ADDRESS` を `MAIL_USERNAME` と**同じアドレス**にします。別のアドレスを指定しても、`Gmail` が自動的にログインしたアドレスへ書き換えます。

> `.env` は `.gitignore` に追加して、Git に登録しません。見本の `.env.sample` には、本物の値を書かずに**ダミーの値**を入れて登録します。

### .env を読み込む

`vlucas/phpdotenv` が、`.env` の内容を `$_ENV` に読み込みます。

```bash
composer require vlucas/phpdotenv
```

```php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$host = $_ENV['MAIL_HOST'];   // smtp.gmail.com
```

| 処理 | 内容 |
| ---- | ---- |
| createImmutable | `.env` のあるフォルダを指定（既存の環境変数は上書きしない） |
| load | `.env` を読み込み、`$_ENV` で使えるようにする |

---

## PHPMailerで送信する

`PHP` 標準の `mail()` 関数は、サーバーのメール設定に依存し、認証や `TLS` を扱えません。そのため `SMTP` で送るためのライブラリ **PHPMailer** を使います。

### インストール

```bash
cd 07_mail
composer require phpmailer/phpmailer
```

`Composer` が `vendor/` を作り、`vendor/autoload.php` を読み込むだけでクラスが使えるようになります。

```php
require __DIR__ . '/vendor/autoload.php';
```

### 最小の送信プログラム

まず、フォームなしで1通だけ送るプログラムで、設定が正しいことを確認します。

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__)->load();

$mail = new PHPMailer(true);

try {
    // SMTP の設定
    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'];
    $mail->Port       = (int) $_ENV['MAIL_PORT'];
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
    $mail->Username   = $_ENV['MAIL_USERNAME'];
    $mail->Password   = $_ENV['MAIL_PASSWORD'];
    $mail->CharSet    = 'UTF-8';

    // 差出人と宛先
    $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    $mail->addAddress('to@example.com', '宛先の名前');

    // 件名と本文
    $mail->Subject = 'テストメール';
    $mail->Body    = "これは PHPMailer から送ったメールです。\n2行目です。";

    $mail->send();
    echo "送信しました";
} catch (Exception $e) {
    echo "送信に失敗しました: " . $mail->ErrorInfo;
}
```

実行は、ターミナルから行えます。

```bash
php send_test.php
```

| 処理 | 内容 |
| ---- | ---- |
| new PHPMailer(true) | `true` を渡すと、エラーを**例外**として扱う |
| isSMTP | `mail()` ではなく `SMTP` で送る指定 |
| SMTPAuth | ログイン（認証）を行う |
| SMTPSecure | 暗号化の方式（`tls` / `ssl`） |
| CharSet | 日本語が文字化けしないよう `UTF-8` を指定 |
| setFrom | 差出人 |
| addAddress | 宛先（複数回呼ぶと複数の宛先になる） |
| send | 送信を実行 |
| ErrorInfo | 失敗した理由の文字列 |

> 例外を受け取る考え方は、06 章の `PDOException` と同じです。`try / catch` で失敗を受け取り、画面にはエラーの理由を表示します。

### 暗号化の指定を定数で書く

`'tls'` や `'ssl'` の文字列の代わりに、`PHPMailer` の定数でも指定できます。

```php
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 587 の場合
$mail->Port       = 587;

// 465 の場合
// $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
// $mail->Port       = 465;
```

### HTMLメールにする

本文を `HTML` にするときは、`isHTML(true)` を指定します。

```php
$mail->isHTML(true);
$mail->Body    = '<h1>こんにちは</h1><p>HTML メールです。</p>';
$mail->AltBody = 'こんにちは。HTML メールです。';
```

| 項目 | 内容 |
| ---- | ---- |
| isHTML(true) | `Body` を `HTML` として送る |
| AltBody | `HTML` を表示できないメールソフト向けの**テキスト版** |

---

## 通信の内容を確認する

送信に失敗したとき、`SMTP` のやり取りを画面に出力すると原因を調べやすくなります。

```php
$mail->SMTPDebug   = SMTP::DEBUG_SERVER;  // use PHPMailer\PHPMailer\SMTP;
$mail->Debugoutput = 'echo';
```

| 値 | 内容 |
| ---- | ---- |
| DEBUG_OFF（0） | 出力しない（通常） |
| DEBUG_CLIENT（1） | クライアントの送信内容だけ |
| DEBUG_SERVER（2） | クライアントとサーバーの両方 |

> **動作確認が終わったら、必ず `DEBUG_OFF` に戻します。** 出力には、サーバーの情報や宛先が含まれます。特に本番の画面には出さないでください。

---

## よくあるエラー

| エラー・症状 | 原因 | 対処 |
| ---- | ---- | ---- |
| `SMTP Error: Could not authenticate.` | ユーザー名やパスワードが違う | `MAIL_USERNAME` と**アプリパスワード**を確認する。通常のパスワードは使えない |
| `Username and Password not accepted` | アプリパスワードではなくログインパスワードを使っている | アプリパスワードを発行して設定する |
| `SMTP connect() failed` | ホスト名・ポート・暗号化の組み合わせが違う | `smtp.gmail.com` / `587` / `tls` を確認する |
| 接続が長く止まる | ネットワークがポートを塞いでいる | 別のネットワークで試す。`465` / `ssl` を試す |
| `Invalid address` | 宛先・差出人のアドレスが不正 | アドレスの形式を確認する |
| 日本語が文字化けする | `CharSet` の指定がない | `$mail->CharSet = 'UTF-8';` を指定する |
| `.env` の値が空になる | 値の中に空白や `#` がある | 値を `"` で囲む |
| メールが届かない | 迷惑メールに分類されている | 迷惑メールフォルダを確認する |

> `Gmail` には**送信数の上限**があります（個人アカウントで 1 日 500 通程度）。短時間に大量に送ると、一時的に送信が止まります。学習用のテストは、少ない回数にとどめます。

---

## セキュリティの注意

| 項目 | 内容 |
| ---- | ---- |
| パスワードはコードに書かない | `.env` に分けて、`.gitignore` で Git 管理の対象外にする |
| 誤って公開したとき | `Google アカウント` でそのアプリパスワードを**削除**し、新しく発行する |
| 宛先を入力値で決めない | フォームで受けたアドレスにそのまま送ると、**迷惑メールの中継**に悪用される |
| ヘッダーインジェクション | 件名や宛先に改行を含む値を入れると、ヘッダーを追加される。`PHPMailer` は対策済みだが、入力のチェックは行う |

> このサンプルは、フォームに入力されたアドレスへ確認メールを送ります。誰でも使える本番のフォームでは、送信回数の制限や、画面上の確認（CAPTCHA など）を加えます。

---

## まとめ

| 項目 | 内容 |
| ---- | ---- |
| SMTP | メールを送信するための通信ルール |
| 送信サーバー | PHP から受け取ったメールを配送する（Gmail は `smtp.gmail.com`） |
| TLS | 通信を暗号化する仕組み |
| STARTTLS | ポート 587。途中から暗号化に切り替える（`tls`） |
| SMTPS | ポート 465。最初から暗号化する（`ssl`） |
| アプリパスワード | 2段階認証のあとに発行する、アプリ専用の 16 桁パスワード |
| .env | パスワードなどの接続情報を、コードと分けて保存するファイル |
| phpdotenv | `.env` を `$_ENV` に読み込むライブラリ |
| PHPMailer | `SMTP` でメールを送信するライブラリ |
| isSMTP / SMTPAuth / SMTPSecure | `SMTP` で送り、認証し、暗号化する設定 |
| CharSet | 日本語メールの文字化けを防ぐ `UTF-8` の指定 |
| SMTPDebug | `SMTP` のやり取りを表示する確認用の設定 |
