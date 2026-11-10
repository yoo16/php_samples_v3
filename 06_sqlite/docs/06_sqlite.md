## アプリの概要

`PDO` と `SQLite` を利用して、タイトル・本文・画像を投稿して一覧表示する画像投稿サイトを作成します。ブラウザから受けたリクエストを `PHP` ページが処理し、すべてのページが `src/db.php` を経由して `SQLite` にアクセスします。

| 機能 | ファイル | 内容 | 使う SQL |
| ---- | ---- | ---- | ---- |
| 接続・初期化 | src/db.php | DB への接続とテーブル作成 | CREATE TABLE |
| 一覧表示 | public/index.php | 最新 20 件を新しい順に表示 | SELECT |
| 投稿 | public/post.php | フォーム入力と画像を保存 | INSERT |
| 削除 | public/delete.php | 投稿と画像を1件削除 | DELETE |
| 初期化 | public/reset.php | DB と画像を削除して作り直し | DROP TABLE |

<img src="/storage/teaching_material/php_sqlite_overview.svg" class="" width="">


### ファイル構成

`06_sqlite` の主なファイル構成は次のとおりです。

```txt
06_sqlite/
├── public/            ← 公開フォルダ（-t public）
│   ├── index.php
│   ├── post.php
│   ├── delete.php
│   ├── reset.php
│   ├── js/
│   │   └── modal.js
│   └── uploads/
├── src/               ← 公開しないプログラム
│   ├── db.php
│   └── components/
│       ├── header.php
│       └── footer.php
└── data/
    └── mypix.sqlite
```

| 場所 | 内容 |
| ---- | ---- |
| public | ブラウザから直接アクセスするファイル |
| public/index.php | 投稿一覧の表示 |
| public/post.php | 投稿フォームと登録処理 |
| public/delete.php | 投稿1件と画像の削除 |
| public/reset.php | DB とアップロード画像の初期化 |
| public/js/modal.js | 画像拡大モーダルの動き |
| public/uploads | アップロードされた画像の保存先 |
| src/db.php | PDO の接続とテーブル作成 |
| src/components | 共通のヘッダーとフッター |
| data/mypix.sqlite | SQLite 本体（自動生成） |

> **ブラウザから見せる必要があるファイルだけを `public` に置きます。** `db.php` や `mypix.sqlite` を `public` の外に置くことで、URL を直接指定されても中身をダウンロードされません。理由は、「公開フォルダを分ける理由」で説明しています。

---

## PHPサーバーとは

### なぜサーバーが必要なのか

`PHP` で作ったページは、HTML ファイルのようにダブルクリックしても動きません。**`PHP` を実行してくれるサーバー**が必要です。

`PHP` のファイルをブラウザで直接開いても、`PHP` のコードはそのまま文字として表示されるだけです。ブラウザは `HTML`、`CSS`、`JavaScript` は理解できますが、`PHP` は理解できません。

`PHP` は**サーバー側で実行**し、その結果の `HTML` だけをブラウザへ返します。

<img src="/storage/teaching_material/php_server_flow.svg" class="" width="">

1. ブラウザが `http://localhost:8000/post.php` のような URL でページを要求する
2. サーバーが `post.php` を見つけ、`PHP` で実行する
3. `PHP` が `SQLite` を読み書きして、`HTML` を組み立てる
4. サーバーが、できあがった `HTML` をブラウザへ返す

| 場所 | 動くもの |
| ---- | ---- |
| サーバー側 | PHP、SQLite、ファイルの保存 |
| ブラウザ側 | HTML の表示、CSS、JavaScript |

> ブラウザには `PHP` のコードは届きません。届くのは実行結果の `HTML` だけです。そのため、`DB` の接続情報などをコードに書いても、ブラウザからは見えません。

### PHPを動かす方法

`PHP` を動かすサーバーには、いくつかの種類があります。

| 方法 | 内容 | 主な用途 |
| ---- | ---- | ---- |
| 組み込みサーバー | PHP に最初から入っている。コマンド1つで起動 | 学習、開発 |
| Apache | 広く使われている Web サーバーに PHP を組み合わせる | 本番、レンタルサーバー |
| Nginx | 軽量な Web サーバーに PHP を組み合わせる | 本番 |
| XAMPP など | Apache と DB などをまとめたパッケージ | 学習、開発 |

このサンプルでは、準備がいちばん少ない**組み込みサーバー**を使います。

---

## 組み込みサーバーを使う

`PHP` には**開発用の Web サーバー**が最初から入っています。`Apache` や `Nginx` をインストールしなくても、コマンド1つで `PHP` ページを表示できます。

### 起動する

```bash
cd 06_sqlite
php -S localhost:8000 -t public
```

| 指定 | 内容 |
| ---- | ---- |
| php -S | 組み込みサーバーを起動する |
| localhost:8000 | 待ち受けるホスト名とポート番号 |
| -t public | ドキュメントルート（公開するフォルダ）を public にする |

起動したら、ブラウザで `http://localhost:8000/` を開きます。サーバーを止めるときは、ターミナルで `Ctrl + C` を押します。

起動中のターミナルは、サーバーが動き続けている状態です。コマンドの入力を受け付けなくなりますが、故障ではありません。

> `-t` と `public` の間は半角スペースです。`-t` を付け忘れると、`public` が**ルータースクリプト**（すべてのリクエストを最初に受け取る `PHP` ファイル）として扱われ、`Failed opening required 'public'` というエラーになります。

### ログを見る

アクセスのたびに、ターミナルへログが表示されます。エラーの確認にも使えます。

```txt
[Mon Nov 10 23:30:00 2026] 127.0.0.1:52000 [200]: GET /
[Mon Nov 10 23:30:05 2026] 127.0.0.1:52001 [200]: GET /uploads/3f2b8c1e.jpg
[Mon Nov 10 23:30:09 2026] 127.0.0.1:52002 [404]: GET /missing.php - No such file or directory
```

| 部分 | 内容 |
| ---- | ---- |
| 127.0.0.1:52000 | アクセス元（自分のパソコン） |
| [200] | ステータスコード（成功） |
| GET / | リクエストの種類と、要求された場所 |
| [404] | ファイルが見つからなかった |

### よくあるエラー

| 症状 | 原因 | 対処 |
| ---- | ---- | ---- |
| Failed opening required 'public' | `-t` を付け忘れた | `-t public` と書く |
| Address already in use | 同じポートを別のプログラムが使用中 | `localhost:8001` のようにポートを変える |
| 404 Not Found | ドキュメントルートの外、または存在しない URL | フォルダの場所とファイル名を確認する |
| ページが空白になる | `PHP` の実行時エラー | ターミナルのログを確認する |

### Apacheなどとの違い

| 項目 | Apache など | 組み込みサーバー |
| ---- | ---- | ---- |
| 準備 | インストールと設定が必要 | PHP だけでよい |
| 公開フォルダ | DocumentRoot で指定 | `-t` で指定 |
| 起動・停止 | サービスの起動・停止 | コマンドの実行・`Ctrl + C` |
| 同時処理 | 多数のアクセスを並行処理 | 基本的に1件ずつ処理 |
| 用途 | 本番、実運用に近い環境 | 学習、開発での動作確認 |

> 組み込みサーバーは開発専用です。性能とセキュリティの面で本番に向かないため、本番環境では `Apache` や `Nginx` を使います。

---

## ドキュメントルートとURL

**ドキュメントルート**とは、URL の `/` に対応するフォルダのことです。ブラウザからは、このフォルダの**中にあるファイルだけ**が URL で指定できます。

`-t public` で起動したときの、URL とファイルの対応は次のとおりです。

| URL | 実際のファイル |
| ---- | ---- |
| http://localhost:8000/ | public/index.php |
| http://localhost:8000/post.php | public/post.php |
| http://localhost:8000/js/modal.js | public/js/modal.js |
| http://localhost:8000/uploads/3f2b8c1e.jpg | public/uploads/3f2b8c1e.jpg |
| http://localhost:8000/src/db.php | **存在しない**（404） |
| http://localhost:8000/data/mypix.sqlite | **存在しない**（404） |

`public` の外にある `src` や `data` は、URL でたどり着く道がありません。ブラウザから見えないのは、隠しているのではなく、**公開範囲の外に置いているから**です。

---

## 公開フォルダを分ける理由

### 公開しないものがある

このアプリには、ブラウザに見せるべきでないファイルがあります。

| ファイル | 見せてはいけない理由 |
| ---- | ---- |
| data/mypix.sqlite | 全投稿のデータそのもの。ダウンロードされると中身がすべて読まれる |
| src/db.php | DB の場所や接続の仕組みなど、内部の作りが分かる |
| src/components | 画面の部品であり、単体で開かれる想定がない |
| .git / .gitignore | 開発の履歴や設定が含まれる |

### 公開フォルダをプロジェクト全体にすると

試しに、`-t` を `public` ではなく**プロジェクト全体**にして起動するとどうなるかを考えます。

```bash
cd 06_sqlite
php -S localhost:8000 -t .
```

| URL | 結果 |
| ---- | ---- |
| http://localhost:8000/data/mypix.sqlite | **DB ファイルがそのままダウンロードされる** |
| http://localhost:8000/src/db.php | 単体で実行され、エラーや内部情報が表示される |
| http://localhost:8000/.gitignore | 設定ファイルの中身が表示される |

ブラウザで URL を入力するだけで、投稿データが丸ごと持ち出せてしまいます。`SQLite` は**ファイル1つがデータベース**であるため、ファイルを公開することは、データベースを公開することと同じです。

> 実際に試す場合は、自分のパソコン内だけで行います。ダウンロードした DB は `sqlite3` コマンドで開くと、投稿の中身が読めることを確認できます。確認が終わったら、必ず `-t public` に戻します。

### public に分ける考え方

そこで、フォルダを役割ごとに分けます。

<img src="/storage/teaching_material/php_public_src_data.svg" class="" width="">

| フォルダ | 置くもの | ブラウザから |
| ---- | ---- | ---- |
| public | 入口となる PHP、JavaScript、画像（uploads） | 直接アクセスできる |
| src | DB 接続や共通部品など、PHP から読み込むプログラム | アクセスできない |
| data | SQLite 本体 | アクセスできない |

`PHP` の `require_once` は、**サーバー内のファイルを直接読みに行く**命令です。URL を経由しないため、`public` の外にあるファイルも問題なく読み込めます。

```php
// public/index.php
require_once __DIR__ . "/../src/db.php";
```

| 処理 | 内容 |
| ---- | ---- |
| __DIR__ | この `index.php` があるフォルダ（public） |
| /../ | 1つ上のフォルダ（06_sqlite）へ戻る |
| src/db.php | public の外にあるファイルを読み込む |

つまり、**ブラウザには入口（`public`）だけを見せ、中身（`src`、`data`）は `PHP` だけが読む**という分担になります。

### uploads は public に置く

アップロードされた画像は、`<img src="uploads/xxx.jpg">` のように、ブラウザが直接取りに来ます。そのため、`uploads` は `public` の中に置きます。

| 保存先 | 画像の表示 | 向いている場合 |
| ---- | ---- | ---- |
| public/uploads | URL でそのまま表示できる | 誰に見せてもよい画像（このサンプル） |
| public の外 | PHP で読み出して返す処理が必要 | 本人にだけ見せたい画像 |

> `public/uploads` に置いた画像は、URL を知っていれば誰でも見られます。ファイル名を `UUID` にしているのは、URL を推測されにくくする効果もあります。ただし、秘密の画像を守る方法ではないため、見せる相手を限る画像は `public` の外に置きます。

### 公開フォルダに置いてはいけないもの

`public` に置くと誰でも取得できます。次のようなものは置きません。

| 置かないもの | 理由 |
| ---- | ---- |
| データベースファイル | 全データが持ち出される |
| パスワードや API キーを書いた設定ファイル | 秘密情報が読まれる |
| バックアップ（`.bak`、`.zip`、`.sql`） | 中身が丸ごと取得される |
| `.git` フォルダ | ソースコードと履歴が復元される |

---

### 本番環境での考え方

本番で `Apache` や `Nginx` を使う場合も、考え方は同じです。**公開フォルダを `public` に向けます。** 設定の名前だけが異なります。

#### Apache

```txt
<VirtualHost *:80>
    ServerName mypix.example.com
    DocumentRoot /var/www/06_sqlite/public
</VirtualHost>
```

#### Nginx

```txt
server {
    listen 80;
    server_name mypix.example.com;
    root /var/www/06_sqlite/public;
    index index.php;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
}
```

| 開発 | Apache | Nginx |
| ---- | ---- | ---- |
| php -S ... -t public | DocumentRoot を public に設定 | root を public に設定 |

> `Nginx` は `PHP` を自分では実行できないため、`php-fpm`（`PHP` を実行する別のプロセス）に処理を渡す設定（`fastcgi_pass`）が必要です。`fastcgi_pass` の接続先やパスは、環境（OS、`PHP` のバージョン）によって異なります。

> 共有レンタルサーバーなどで `public` を指定できない場合は、`data` に `.htaccess` で `Require all denied` を書くなどして、直接のアクセスを拒否します。ただし、**最初から公開範囲の外に置く方が確実**です。

---

## SQLiteとは

### SQLiteの特徴

`SQLite` は、ファイル1つで使える軽量な `データベース` です。サーバーの準備が不要なため、学習用の小さなアプリや、設定を増やしたくない場面でよく使われます。

`SQLite` は、`MySQL` や `PostgreSQL` のようなデータベースサーバーを使わず、**1つのファイルにデータを保存する**データベースです。

| 項目 | SQLite | MySQL / PostgreSQL |
| ---- | ---- | ---- |
| サーバー | 不要 | 別プロセスのサーバーが必要 |
| 接続先 | ファイルパス | ホスト、ポート、ユーザー、パスワード |
| 向いている用途 | 学習、小規模アプリ、組み込み | 多人数が同時に書き込むサービス |
| 型 | 緩い（動的型付け） | 厳密 |

> `PHP` からは `new PDO("sqlite:ファイルパス")` と書くだけで接続できます。接続先を変えれば、`PDO` のコードの大部分はそのまま他の DB でも使えます。

### 必要な環境

`PHP` の `pdo_sqlite` 拡張が有効になっている必要があります。

```bash
php -m | grep -i sqlite
```

無効の場合は、`php.ini` で有効化します。

```ini
extension=pdo_sqlite
extension=sqlite3
```

> `data/mypix.sqlite` と `public/uploads/` は `.gitignore` で Git 管理の対象外にする必要があります。

---

## データベース接続

`src/db.php` は、各ページから `require_once` で読み込まれ、`$pdo` を使える状態にします。

<img src="/storage/teaching_material/php_sqlite_db_connect_flow.svg" class="" width="">

処理の流れは次のとおりです。

1. `file_exists()` でファイルの有無を記録する
2. `new PDO()` で `SQLite` に接続する
3. 新規ファイルのときだけ `createTables()` を呼ぶ

### PDOで接続する

```php
$dbFile = __DIR__ . "/../data/mypix.sqlite";
$newDb = !file_exists($dbFile);

$pdo = new PDO("sqlite:" . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($newDb) {
    createTables();
}
```

| 処理 | 内容 |
| ---- | ---- |
| __DIR__ | 実行場所に依存しない、このファイルのフォルダ |
| ../data | src の1つ上にある data フォルダ |
| file_exists | DB ファイルが既にあるかを判定 |
| new PDO | SQLite に接続、無ければファイルを作成 |
| ERRMODE_EXCEPTION | SQL のエラーを例外として扱う設定 |

> `file_exists()` は、必ず `new PDO` の**前**に呼びます。接続した時点でファイルができてしまい、新規かどうかを判定できなくなるためです。

### 例外を処理する

`ERRMODE_EXCEPTION` を指定すると、接続や SQL のエラーが `PDOException` になります。`try / catch` で受け取り、エラーメッセージを表示できます。

```php
try {
    $pdo = new PDO($schema);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "DB接続エラー: " . htmlspecialchars($e->getMessage());
}
```

---

## テーブル設計

`createTables()` で、投稿を保存する `notices` テーブルを作成します。

```php
function createTables()
{
    global $pdo;
    $ddl = "CREATE TABLE IF NOT EXISTS notices (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                body TEXT,
                image TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )";
    $pdo->exec($ddl);
}
```

| 列 | 内容 | 補足 |
| ---- | ---- | ---- |
| id | 投稿 ID | 自動採番の主キー |
| title | タイトル | NOT NULL |
| body | 本文 | 省略可 |
| image | 画像のパス | uploads/xxx.jpg のような文字列 |
| created_at | 投稿日時 | 省略時は現在時刻が入る |

`exec()` は、結果を返さない SQL（`CREATE`、`DROP` など）を実行するメソッドです。

> **画像そのものは DB に入れず、`uploads/` に保存してパスだけを DB に記録します。** DB が大きくならず、画像をそのまま配信できるためです。

> `CURRENT_TIMESTAMP` は **UTC** で保存されます。日本時間とは 9 時間ずれる点に注意が必要です。

---

## 一覧表示

`public/index.php` は、`notices` から最新の投稿を取り出して、カード形式で表示します。

<img src="/storage/teaching_material/php_sqlite_list_flow.svg" class="" width="">

処理の流れは次のとおりです。

1. ブラウザが `index.php` を開く
2. `SELECT` で投稿を取得する
3. `fetchAll()` で結果を配列として受け取る
4. `foreach` で HTML に変換する
5. カード一覧として表示する

### SELECTで取得する

```php
$sql = "SELECT * FROM notices ORDER BY created_at DESC LIMIT 20";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

| 要素 | 内容 |
| ---- | ---- |
| ORDER BY created_at DESC | 新しい順に並べる |
| LIMIT 20 | 最大 20 件に絞る |
| query | 値の差し込みがない固定の SQL を実行 |
| fetchAll | 全行を配列として取得 |
| FETCH_ASSOC | 列名をキーにした連想配列で受け取る |

### 画面に表示する

取得した `$posts` を `foreach` で回し、カードを出力します。

```php
<?php foreach ($posts as $post): ?>
    <h3><?= htmlspecialchars($post['title']) ?></h3>
    <p><?= date("Y年m月d日 H:i", strtotime($post['created_at'])) ?></p>
<?php endforeach; ?>
```

`htmlspecialchars()` は、入力された文字を安全に HTML へ出力するための関数です。`<script>` などを埋め込まれる **XSS** を防ぐため、ユーザーが入力した値を表示するときは必ず通します。

### 画像モーダル

カードをクリックすると、`public/js/modal.js` の `openModal()` が呼ばれ、画像を拡大表示します。

```javascript
function openModal(imageUrl, title, body) {
    document.getElementById("modalImage").src = imageUrl;
    document.getElementById("modalTitle").innerText = title;
    document.getElementById("modalBody").innerHTML = body;
    document.getElementById("imageModal").classList.remove("hidden");
    document.getElementById("imageModal").classList.add("flex");
}
```

`PHP` が出力した値を `JavaScript` に渡して表示を切り替えるだけで、**サーバーとの通信は発生しません。**

---

## 投稿処理

`public/post.php` は、フォームの表示と、`POST` されたときの登録処理を1つのファイルで行います。

<img src="/storage/teaching_material/php_sqlite_post_flow.svg" class="" width="">

処理の流れは次のとおりです。

1. フォームを送信する
2. `trim()` でタイトルと本文を取得する
3. 画像があれば `uploads/` に保存する
4. タイトルか画像があるかを確認する
5. `INSERT` で登録する
6. `header()` で一覧へリダイレクトする

### 画像を保存する

```php
$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif'];

if (in_array($ext, $allowed)) {
    $fileName = uuidv4() . "." . $ext;
    $targetFile = $uploadDir . $fileName;

    move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    $imagePath = "uploads/" . $fileName;
}
```

| 処理 | 内容 |
| ---- | ---- |
| pathinfo | 元のファイル名から拡張子だけを取り出す |
| in_array | 許可した拡張子だけを通す |
| uuidv4 | 重複しないランダムな名前を生成する自作関数 |
| move_uploaded_file | 一時ファイルを uploads へ移動 |

保存するファイル名は `3f2b8c1e-9a47-4d0b-8e65-1c2a7f4b9d10.jpg` のような形になります。**元のファイル名は使いません。** 同じ名前の画像を投稿しても上書きされず、日本語や空白、`../` を含む名前も URL やパスに残りません。

### UUIDを生成する

`PHP` には `UUID` を作る標準関数がないため、`public/post.php` の先頭で `uuidv4()` を定義しています。

```php
function uuidv4(): string
{
    $b = random_bytes(16);
    $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
    $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
}
```

| 処理 | 内容 |
| ---- | ---- |
| random_bytes(16) | 暗号用の乱数を 16 バイト生成 |
| $b[6] の設定 | バージョン 4（乱数方式）を示すビットを設定 |
| $b[8] の設定 | UUID の規格を示すビットを設定 |
| bin2hex | バイト列を 16 進数の文字列に変換 |
| vsprintf | 8-4-4-4-12 桁のハイフン区切りに整形 |

> `UUID` は、世界中でほぼ重複しない ID を作るための標準形式です。`Composer` の `ramsey/uuid` を使うと `Uuid::uuid4()` の1行で生成できますが、この章は外部ライブラリなしで動かすため自作しています。

### multipart/form-data とは

`HTML` のフォームは、`enctype` を省略すると `application/x-www-form-urlencoded` で送信されます。この形式では、ファイルを選んでも**ファイル名の文字列しか送られず、中身は届きません。** 画像を送るフォームには、`enctype="multipart/form-data"` を指定します。

| 送り方 | 画像の受け取り方 | 補足 |
| ---- | ---- | ---- |
| form の enctype に multipart/form-data | `$_FILES` | このサンプルの方法 |
| JavaScript の `fetch` + `FormData` | `$_FILES` | `enctype` の記述は不要。ブラウザが自動で multipart にする |
| JavaScript で画像をそのまま body に入れる | `php://input` | `$_FILES` は空。自分で保存処理を書く |
| Base64 文字列にして JSON で送る | `json_decode` | 容量が約 1.3 倍になる |

つまり、`HTML` のフォームから `$_FILES` で受け取る場合に `enctype` が必要で、`JavaScript` で送るなら `FormData` が同じ役割を果たします。

```javascript
const formData = new FormData();
formData.append("title", "タイトル");
formData.append("image", fileInput.files[0]);
fetch("post.php", { method: "POST", body: formData });
```

> `fetch` で `FormData` を送るとき、`Content-Type` ヘッダーを自分で書いてはいけません。境界を表す `boundary` が抜けて、`PHP` が解析できなくなります。

### multipart 受信の注意点

`multipart/form-data` は、`PHP` が本体を解析してから `$_POST` と `$_FILES` に振り分けます。アップロードを受け付けると、次の点に注意が必要です。

| 項目 | 内容 | 対策 |
| ---- | ---- | ---- |
| 巨大なリクエスト | 大きな本体や大量のパートで、サーバーの CPU やメモリ、ディスクを消費させられる（過去には `PHP` の multipart 解析そのものの脆弱性で、CPU を使い切る DoS が報告された） | `PHP` を最新版に保つ。`post_max_size`、`upload_max_filesize`、`max_file_uploads` を必要最小限にする |
| サイズ超過の無言の失敗 | `post_max_size` を超えると、`$_POST` と `$_FILES` が**空になり、エラーも出ない** | `$_SERVER['CONTENT_LENGTH']` と `$_FILES['image']['error']` を確認する |
| クライアントが決める値 | `$_FILES['image']['name']` と `type` は偽装できる | 名前は使わず、中身を `getimagesize` などで確認する |
| 一時ファイルが残る | 処理を中断しても一時ファイルが作られる | `move_uploaded_file` で保存するか、終了時に消えることを前提にする |

```bash
php -r 'echo ini_get("upload_max_filesize"), " ", ini_get("post_max_size"), "\n";'
```

> このサンプルは、`post_max_size`（初期値 8M）を超えた場合や `$_FILES['image']['error']` の確認をしていません。大きな画像を送ると、何も起きずに投稿が失敗したように見えます。

### プレースホルダで登録する

```php
$sql = "INSERT INTO notices (title, body, image) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$title, $body, $imagePath]);
```

| 処理 | 内容 |
| ---- | ---- |
| $sql | 実行する SQL を変数にまとめる |
| prepare | SQL を準備してステートメントを返す |
| execute | 配列の値を ? に順番に入れて実行 |

`?` の部分に、`execute()` の配列の値が順番に入ります。値は SQL とは別に渡されるため、**入力内容が SQL として解釈されることはありません。**

```php
// 危険：文字列連結（SQL インジェクション）
$sql = "INSERT INTO notices (title) VALUES ('" . $title . "')";
$pdo->query($sql);

// 安全：プレースホルダ
$sql = "INSERT INTO notices (title) VALUES (?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$title]);
```

画像が選ばれなかった場合、`$imagePath` は `null` のままなので、`image` 列には `NULL` が保存されます。

> SQL を `$sql` に入れてから実行する書き方は、`index.php`、`delete.php`、`reset.php` でも共通です。実行する SQL が変数として見えるため、内容を確認しやすくなります。

### 登録後にリダイレクトする

登録が終わったら、`header()` で `index.php` へ移動します。

```php
$stmt->execute([$title, $body, $imagePath]);
header("Location: index.php?posted=1");
exit;
```

| 処理 | 内容 |
| ---- | ---- |
| header Location | 指定したページへ移動するよう、ブラウザに指示 |
| ?posted=1 | 投稿後であることを一覧ページへ伝える目印 |
| exit | 以降の処理を止める |

投稿後にそのまま画面を表示すると、ブラウザの再読み込みでフォームが**再送信され、同じ投稿が二重に登録**されます。登録後に別のページへ移動させ、再読み込みは移動先の `GET` として扱わせることで、二重登録を防げます。

> `POST` のあとにリダイレクトして `GET` で表示する流れを、PRG パターンと呼びます。`delete.php`、`reset.php` でも同じ方法を使っています。

> `header()` は、画面への出力より前に実行する必要があります。呼んだあとは、必ず `exit` で処理を終えます。

---

## 削除

`public/delete.php` は、投稿を1件削除し、紐づく画像ファイルも削除します。

```php
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

$stmt = $pdo->prepare("SELECT image FROM notices WHERE id = ?");
$stmt->execute([$id]);
$image = $stmt->fetchColumn();

$pdo->prepare("DELETE FROM notices WHERE id = ?")->execute([$id]);

if (is_string($image) && $image !== "") {
    $uploadDir = realpath(__DIR__ . "/uploads");
    $file = realpath(__DIR__ . "/" . $image);
    if ($uploadDir !== false && $file !== false
        && str_starts_with($file, $uploadDir . DIRECTORY_SEPARATOR) && is_file($file)) {
        unlink($file);
    }
}

header("Location: index.php?deleted=1");
exit;
```

| 処理 | 内容 |
| ---- | ---- |
| FILTER_VALIDATE_INT | id が整数かを検証 |
| SELECT image | DELETE の前に画像のパスを取得 |
| DELETE | プレースホルダでレコードを削除 |
| realpath / str_starts_with | uploads 内のファイルだけを削除対象にする |
| unlink | 画像ファイルを削除 |

> レコードを先に消すと画像のパスが分からなくなるため、**先に `SELECT` で取得しておきます。**

---

## 初期化

`public/reset.php` は、テーブルを削除して作り直し、アップロード画像も削除します。

```php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$sql = "DROP TABLE IF EXISTS notices";
$pdo->exec($sql);
createTables();

foreach (glob(__DIR__ . "/uploads/*") as $file) {
    if (is_file($file)) {
        unlink($file);
    }
}

header("Location: index.php?reset=1");
exit;
```

| 処理 | 内容 |
| ---- | ---- |
| POST の判定 | URL を開いただけでは削除されないようにする |
| DROP TABLE | $sql に入れた SQL でテーブルを削除 |
| createTables | テーブルを作り直す |
| glob / unlink | uploads 内の画像を削除 |
| header Location | 完了後に一覧へ戻す |

ヘッダーの「DB初期化」ボタンには、`confirm()` による確認ダイアログを付けています。処理後に一覧へリダイレクトすることで、再読み込みによる二重実行を防げます。

---

## まとめ

| 項目 | 内容 |
| ---- | ---- |
| SQLite | ファイル1つで使える軽量なデータベース |
| PDO | PHP のデータベース共通インターフェース |
| DSN | 接続先を表す文字列（sqlite:ファイルパス） |
| ERRMODE_EXCEPTION | SQL のエラーを例外として扱う設定 |
| exec | 結果を返さない SQL を実行するメソッド |
| query | 固定の SELECT を実行するメソッド |
| prepare / execute | 値と SQL を分けて実行する仕組み |
| プレースホルダ | SQL 内の値の差し込み位置（?） |
| fetchAll | 取得結果をすべて配列で受け取るメソッド |
| htmlspecialchars | HTML 出力時のエスケープ関数（XSS 対策） |
| uploads | 画像ファイルの保存先（DB にはパスだけ保存） |
| PHP サーバー | PHP を実行して、結果の HTML を返すサーバー |
| php -S ... -t public | 組み込みサーバーの起動（開発専用、public を公開） |
| ドキュメントルート | URL の / に対応する、公開するフォルダ |
| require_once / __DIR__ | URL を経由せず、public の外のファイルを読み込む |
| public / src / data | 公開するファイルと、公開しないファイルの分離 |
| PRG パターン | POST 後にリダイレクトして二重送信を防ぐ手法 |
