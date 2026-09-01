## Composerの基本ファイル

`Composer` を使うと、プロジェクトの中にいくつかのファイルやフォルダが自動的に作られます。まずはこれらの役割を理解しておくと、後の説明が分かりやすくなります。

### composer.json

`composer.json` は、**このプロジェクトがどのライブラリを必要としているかを書いた設計図**です。

```bash
composer require パッケージ名
```

を実行すると、この中に自動で追記されていきます。

```json
{
    "require": {
        "vlucas/phpdotenv": "^5.6",
        "guzzlehttp/guzzle": "^7.14"
    }
}
```

### composer.lock

`composer.lock` は、**実際にインストールされたライブラリの正確なバージョンを記録したファイル**です。チームで開発するとき、このファイルがあると全員が同じバージョンを使えます。

### vendorフォルダ

`vendor` フォルダは、**ダウンロードされたライブラリの本体が入る場所**です。`composer require` を実行すると、ライブラリのプログラムがここに保存されます。

> vendor フォルダは Composer が自動生成するため、自分で中身を編集したり、Git 管理する必要はありません。

### 各ファイルの役割まとめ

| ファイル・フォルダ | 役割 |
| ---- | ---- |
| composer.json | 必要なライブラリを書いた設計図 |
| composer.lock | 実際に入れたバージョンの記録 |
| vendor | ライブラリ本体の保存場所 |
| vendor/autoload.php | ライブラリを読み込むための入口ファイル |

---

## Composerのコマンド

`Composer` はコマンドで操作します。初学者がまず覚えておきたい基本コマンドを整理します。

### よく使うコマンド一覧

| コマンド | 内容 |
| ---- | ---- |
| composer init | composer.json の新規作成 |
| composer require パッケージ名 | ライブラリの追加とインストール |
| composer install | composer.lock をもとにした一括インストール |
| composer update | ライブラリを最新バージョンへ更新 |
| composer remove パッケージ名 | ライブラリの削除 |
| composer dump-autoload | オートローダーの再生成 |

### requireとinstallの違い

初学者がつまずきやすいのが `composer require` と `composer install` の違いです。

- `composer require` は、**新しいライブラリを追加するとき**に使う
- `composer install` は、**すでに `composer.json` に書かれたライブラリをまとめて入れるとき**に使う

たとえば他の人が作ったプロジェクトをダウンロードしてきたときは、`vendor` フォルダが無いことがあります。その場合は `composer install` を実行すれば、必要なライブラリが一括でそろいます。

> composer install は composer.lock を優先して読み込むため、チーム全員が同じバージョンを再現できます。

---

## オートローダーの仕組み

ライブラリを `vendor` フォルダに入れただけでは、まだプログラムから使えません。そのライブラリを「読み込む」必要があります。この読み込みを自動で行ってくれるのが**オートローダー**です。

### autoload.phpの役割

`Composer` は `vendor/autoload.php` というファイルを自動で作ります。このファイルを1つ読み込むだけで、`vendor` フォルダにあるすべてのライブラリが使えるようになります。

```php
<?php
require_once 'vendor/autoload.php';
```

**1行読み込むだけで、必要なクラスが自動的に使えるようになる**のがオートローダーの便利なところです。読み込みたいライブラリごとに `require` を書く必要はありません。

### パスの指定が難しい理由

今回のサンプルは、次のようなフォルダ構成になっています。`vendor` フォルダは各サンプルの2つ上の階層にまとめて置かれています。

```txt
fin/
├── vendor/                 ← ライブラリ本体（共通）
├── bootstrap.php           ← 共通の読み込みファイル
└── 01_composer/
    ├── env_sample/
    │   └── index.php       ← ここから vendor を読み込みたい
    └── guzzle_sample/
        └── index.php
```

`env_sample/index.php` から見ると `vendor` は2つ上の階層にあり、`../../` が続くと次のような分かりにくいパスになります。

```php
<?php
require_once __DIR__ . '/../../vendor/autoload.php';
```

---

## bootstrap.phpによる共通化

パスの指定を分かりやすくし、共通の初期化処理を1か所にまとめるために、`bootstrap.php` という共通ファイルを用意します。

### bootstrap.phpの中身

`fin` フォルダの直下に `bootstrap.php` を置き、その中でオートローダーを読み込みます。

```php
<?php
// 全サンプル共通の初期化ファイル
define('BASE_DIR', __DIR__);

// Composer のオートローダーを読み込む
require_once BASE_DIR . '/vendor/autoload.php';
```

ここでは `dirname()` を使って階層を分かりやすく指定しています。

| 書き方 | 意味 |
| ---- | ---- |
| __DIR__ | このファイルがあるフォルダ |
| dirname(__DIR__, 2) | 2つ上のフォルダ |
| BASE_DIR | プロジェクトの基準となるフォルダの定数 |

### 各サンプルからの読み込み

各サンプルの `index.php` からは、`bootstrap.php` を1行読み込むだけで済みます。

```php
<?php
// 共通の初期化ファイルを読み込む（Composer オートローダーなど）
require_once dirname(__DIR__, 2) . '/bootstrap.php';
```

> 今後 DB 接続や共通関数が増えても、bootstrap.php に追記するだけで全サンプルへ反映できます。

---

## サンプル1: 環境変数を読み込む

1つ目のサンプルは、`.env` ファイルから設定値を読み込む `env_sample` です。`API` キーやデータベースのパスワードなど、**外部に見せたくない情報をコードの外に切り出す**ために使います。

### phpdotenvのインストール

`.env` ファイルを扱うために、`vlucas/phpdotenv` というライブラリを使います。

```bash
composer require vlucas/phpdotenv
```

### phpdotenvの役割

`API` キーやパスワードのような秘密の情報を、プログラムのコードの中に直接書いてしまうと、`GitHub` などに公開したときに漏れてしまう危険があります。

そこで、こうした設定値だけを `.env` という別ファイルにまとめておき、プログラムからはそれを読み込んで使うようにします。この `.env` ファイルを簡単に読み込んでくれるのが `phpdotenv` です。

| 項目 | 内容 |
| ---- | ---- |
| phpdotenv | .env ファイルを読み込む PHP ライブラリ |
| .env | 設定値を「キー=値」の形で書いたファイル |
| $_ENV | 読み込んだ値が入る PHP の配列 |

### .envファイル

設定値は `.env` ファイルに `キー=値` の形式で書きます。

```txt
API_KEY=1234abcd5678efgh90ijklmnopqrstuv
DB_HOST=localhost
DB_PORT=5432
DB_USER=admin
DB_PASSWORD=securepassword123
DB_NAME=mydatabase
```

.env ファイルは Git 管理から外し（.gitignore）、**外部に公開しない**ようにします。代わりに .env.sample などの見本を用意するのが一般的です。

### .envの読み込み

`Dotenv::createImmutable()` で `.env` ファイルを読み込む準備をし、`load()` で実際に読み込みます。

```php
// __DIR__（このファイルと同じフォルダ）にある .env を対象にする
$dotenv = Dotenv::createImmutable(__DIR__);
// 実際に読み込む
$dotenv->load();
```

`createImmutable()` に渡す `__DIR__` は「`.env` ファイルがどのフォルダにあるか」を表します。ここを間違えると `.env` が見つからずエラーになるため、**置き場所とパスを合わせる**ことが大切です。

`createImmutable`（イミュータブル＝変更不可）は、一度読み込んだ値を後から上書きしない安全な読み込み方法です。

### $_ENV

`load()` を実行すると、`.env` に書いた値が `$_ENV` という配列に自動で入ります。たとえば `.env` に `API_KEY=...` と書いておけば、`$_ENV['API_KEY']` で取り出せます。

```php
echo $_ENV['API_KEY']; // .env の API_KEY の値
```

このサンプルでは、後から使いやすいように、その値を `define()` で定数へ変換しています。

```php
define('API_KEY', $_ENV['API_KEY']);
```

### プログラムの流れ

`Dotenv` ライブラリで `.env` を読み込み、値を定数として定義します。
このとき定数の動的設定は、`define()` を利用します。

```php
<?php
// 共通の初期化ファイルを読み込む（Composer オートローダーなど）
require_once dirname(__DIR__, 2) . '/bootstrap.php';

// 環境変数を読み込むための Dotenv ライブラリを使用
use Dotenv\Dotenv;

// .env ファイルから環境変数を読み込む
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// define() で環境変数を定数として定義
define('API_KEY', $_ENV['API_KEY']);
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('DB_NAME', $_ENV['DB_NAME']);
```

### 動作確認

<img src="/storage/teaching_material/php_dotenv.png" class="" width="500">

---

## サンプル2: 外部APIと通信する

2つ目のサンプルは、`Guzzle` を使って外部の `Web API` と通信する `guzzle_sample` です。`GitHub` の `API` からユーザー情報を取得して表示します。

### Guzzleのインストール

`HTTP` 通信を簡単に行うために、`guzzlehttp/guzzle` というライブラリを使います。

```bash
composer require guzzlehttp/guzzle
```

### Guzzleの役割

`PHP` にも標準の通信機能はありますが、コードが複雑になりがちです。`Guzzle` を使うと、**少ないコードで安全に `HTTP` 通信ができる**ようになります。

| 項目 | 内容 |
| ---- | ---- |
| Guzzle | PHP 向けの HTTP クライアントライブラリ |
| HTTP クライアント | サーバーへリクエストを送り応答を受け取る仕組み |
| Web API | HTTP を使ってデータをやり取りする窓口 |
| JSON | API でよく使われるデータ形式 |

### Guzzleの使い方の基本

`Guzzle` の通信は、大きく3つのステップに分けられます。

| ステップ | 内容 | 使うもの |
| ---- | ---- | ---- |
| ① 準備 | 通信の設定をした窓口を作る | `new Client()` |
| ② 送信 | リクエストを送って応答を受け取る | `$client->get()` |
| ③ 取り出し | 応答の中身（JSON）を取り出す | `getBody()` |

まず `new Client()` で通信用の `Client`（クライアント＝窓口）を作ります。このとき `base_uri`（基準となる `URL`）や `timeout`（何秒待つか）といった共通の設定をまとめて渡せます。

```php
$client = new Client([
    'base_uri' => 'https://api.github.com/', // 基準の URL
    'timeout'  => 5.0,                        // 5秒で通信を打ち切る
]);
```

次に `get()` で `GET` リクエストを送ります。`base_uri` からの続きだけを書けばよいので、パスが短くなります。返ってきた応答（レスポンス）から `getBody()` で本文を取り出し、`JSON` を `json_decode()` で `PHP` の配列に変換します。

```php
$response = $client->get("users/octocat"); // ② 送信
$body = $response->getBody()->getContents(); // ③ 本文を取り出す
$user = json_decode($body, true);            // JSON → 配列に変換
```

### プログラムの流れ

`index.php` では、入力されたユーザー名をもとに `GitHub API` へリクエストを送ります。

```php
<?php
// 共通の初期化ファイルを読み込む（Composer オートローダーなど）
require_once dirname(__DIR__, 2) . '/bootstrap.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

$user = null;   // 取得したユーザー情報
$error = null;  // エラーメッセージ

// 入力されたユーザー名（未入力なら octocat）
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
```

### 動作確認
<img src="/storage/teaching_material/php_guzzle_github.png" class="" width="500">

処理の流れは次のとおりです。

1. 通信の設定をした `Client` を作る
2.  `get()` でリクエストを送る
3. 返ってきた `JSON` を `json_decode()` で配列に変換する
4. 通信に失敗したときは `catch` でエラーを受け取る

> 外部との通信は必ず失敗する可能性があります。try と catch でエラーを受け取り、画面が止まらないようにしておくことが大切です。

---

## まとめ

| 項目 | 内容 |
| ---- | ---- |
| Composer | PHP のライブラリを管理するツール |
| composer.json | 必要なライブラリを書いた設計図 |
| vendor | ライブラリ本体の保存場所 |
| autoload.php | ライブラリを読み込む入口ファイル |
| bootstrap.php | 共通の初期化処理をまとめたファイル |
| phpdotenv | .env から環境変数を読み込むライブラリ |
| Guzzle | 外部 API と通信する HTTP クライアント |
