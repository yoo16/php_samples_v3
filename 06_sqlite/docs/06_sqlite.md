## PHPでSQLiteを使う

`SQLite` は、ファイル1つで使える軽量な `データベース` です。サーバーの準備が不要なため、学習用の小さなアプリや、設定を増やしたくない場面でよく使われます。

今回は `PDO` と `SQLite` を利用して、タイトル・本文・画像を投稿して一覧表示する画像投稿サイト「MyPix」を作りながら、データベース操作の基本を学びます。

| 区分 | 役割 | 主に使うもの |
| ---- | ---- | ---- |
| 接続・初期化 | DB への接続とテーブル作成 | src/db.php、PDO |
| 一覧表示 | 投稿を新しい順に表示 | public/index.php、SELECT |
| 投稿 | フォーム入力と画像を保存 | public/post.php、INSERT、uploads |
| 削除 | 投稿と画像を1件削除 | public/delete.php、DELETE |
| 初期化 | DB と画像を削除して作り直し | public/reset.php |

---

## SQLiteとは

### SQLiteの特徴

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

> このサンプルは外部ライブラリを使いません。`Composer` のインストールは不要です。

### 実行方法

`PHP` には**開発用の組み込みサーバー**があり、`Apache` や `Nginx` を用意しなくてもページを動かせます。

```bash
cd 06_sqlite
php -S localhost:8000 -t public
```

| 指定 | 内容 |
| ---- | ---- |
| php -S | 組み込みサーバーを起動する |
| localhost:8000 | 待ち受けるホスト名とポート番号 |
| -t public | ドキュメントルート（公開するフォルダ）を public にする |

起動したら、ブラウザで `http://localhost:8000/` を開きます。`data/mypix.sqlite` が自動で作成されます。サーバーを止めるときは、ターミナルで `Ctrl + C` を押します。

> `-t` と `public` の間は半角スペースです。`-t` を付け忘れると、`public` がルータースクリプトとして扱われ、`Failed opening required 'public'` というエラーになります。

`Apache` を使う場合との違いは次のとおりです。

| 項目 | Apache など | 組み込みサーバー |
| ---- | ---- | ---- |
| 準備 | インストールと設定が必要 | PHP だけでよい |
| 公開フォルダ | DocumentRoot で指定 | `-t` で指定 |
| 起動・停止 | サービスの起動・停止 | コマンドの実行・`Ctrl + C` |
| 用途 | 本番、実運用に近い環境 | 学習、開発での動作確認 |

> 組み込みサーバーは開発専用です。本番環境では使わず、`Apache` や `Nginx` を使います。

> `data/mypix.sqlite` と `public/uploads/` は `.gitignore` で Git 管理の対象外にしています。

---

## MyPixの全体像

このサンプルでは、次の4つの機能を扱います。

| ファイル | 内容 | 使う SQL |
| ---- | ---- | ---- |
| src/db.php | 接続とテーブル作成 | CREATE TABLE |
| public/index.php | 最新 20 件を表示 | SELECT |
| public/post.php | 投稿を保存 | INSERT |
| public/delete.php | 投稿を1件削除 | DELETE |
| public/reset.php | 全削除して作り直し | DROP TABLE |

<img src="/storage/teaching_material/php_sqlite_overview.svg" class="" width="600">

ブラウザから受けたリクエストを `PHP` ページが処理し、すべてのページが `src/db.php` を経由して `SQLite` にアクセスします。

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

> **ブラウザから見せる必要があるファイルだけを `public` に置きます。** `db.php` や `mypix.sqlite` を `public` の外に置くことで、URL を直接指定されても中身をダウンロードされません。画像は `public/uploads` にあるため、そのまま表示できます。

---

## データベース接続

`src/db.php` は、各ページから `require_once` で読み込まれ、`$pdo` を使える状態にします。

<img src="/storage/teaching_material/php_sqlite_db_connect_flow.svg" class="" width="700">

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

<img src="/storage/teaching_material/php_sqlite_list_flow.svg" class="" width="700">

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

<img src="/storage/teaching_material/php_sqlite_post_flow.svg" class="" width="700">

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

> 画像を送るフォームには、`enctype="multipart/form-data"` が必須です。

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

## 動作確認

1. `http://localhost:8000/` を開き、「まだ投稿はありません」と表示されることを確認する
2. `data/mypix.sqlite` が自動で作成されていることを確認する
3. タイトルのみ、画像のみ、両方で投稿する
4. 投稿後に一覧へ移動し、再読み込みしても二重登録されないことを確認する
5. タイトルも画像も無い投稿が拒否されることを確認する
6. `.txt` など許可外のファイルが拒否されることを確認する
7. 新しい投稿が先頭に並ぶことを確認する
8. 投稿の「削除」で、その投稿と画像だけが消えることを確認する
9. 「DB初期化」で投稿と画像が消えることを確認する

`SQLite` の中身は、コマンドでも確認できます。

```bash
sqlite3 data/mypix.sqlite
```

```sql
.schema notices
SELECT id, title, image, created_at FROM notices;
.quit
```

### 演習

| 番号 | 内容 |
| ---- | ---- |
| 1 | LIMIT を 6 に変えて、表示件数の変化を確認 |
| 2 | 古い順に表示するよう ORDER BY を変更 |
| 3 | title が NOT NULL なのに、題名なしで画像だけ登録できる理由を考察 |
| 4 | 表示時刻が 9 時間ずれる原因と直し方を考案 |
| 5 | author 列を追加し、フォームと一覧に反映 |
| 6 | delete.php の処理を読み、画像を先に消さず SELECT を先にする理由を考察 |
| 7 | LIMIT と OFFSET でページネーションを実装 |

> 演習 3 のヒントは、`NULL` と空文字 `""` の違いです。演習 5 の列追加には `ALTER TABLE notices ADD COLUMN author TEXT;` を使います。

### 改善ポイント

教材としては動作しますが、実運用では次の課題があります。

| 箇所 | 問題 | 対策 |
| ---- | ---- | ---- |
| 拡張子のみのチェック | 拡張子を偽装したファイルを通せる | 中身を検証（getimagesize など） |
| onclick への値の埋め込み | HTML 用のエスケープでは JS として不十分 | data 属性や json_encode を利用 |
| 画像サイズの上限なし | 大きなファイルで容量を圧迫 | サイズの上限を確認 |
| reset.php が認証なし | 誰でも全削除できる | 認証を付ける、または削除 |
| 権限 0777 | 権限が広すぎる | 0755 などに絞る |
| delete.php が認証なし | 誰でも投稿を削除できる | 認証を付ける |

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
| php -S ... -t public | 組み込みサーバーの起動（public を公開） |
| public / src / data | 公開するファイルと、公開しないファイルの分離 |
| PRG パターン | POST 後にリダイレクトして二重送信を防ぐ手法 |
