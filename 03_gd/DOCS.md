## GDライブラリとは

`GD` は、**PHP でプログラムから画像を作ったり加工したりするためのライブラリ**です。写真の縮小やモザイク処理、文字や図形の描き込み、サムネイルの生成などを、画像編集ソフトを使わずコードだけで行えます。

このフォルダでは、`GD` を使った2つのサンプルを扱います。

| サンプル | 内容 | 使うもの |
| ---- | ---- | ---- |
| mosic | アップロードした画像をピクセル風（モザイク）に変換 | GD（PHP 標準） |
| qrcode | 入力した URL から QR コード画像を生成 | endroid/qr-code |

---

## GD拡張を有効にする

`GD` は PHP に付属していますが、**拡張機能として有効化しないと使えません**。`php.ini` を編集します。

### php.iniの場所を確認

```bash
php --ini
```

`Loaded Configuration File` に表示されるパスが、いま使われている `php.ini` です。

### 設定を有効化

`php.ini` を開き、次の行を探します。先頭に `;`（セミコロン）が付いていたら削除します。

```ini
extension=gd
```

`;extension=gd` → `extension=gd` に変更して保存し、**Web サーバー（Apache など）を再起動**します。

### 有効になったか確認

```bash
php -m | grep gd
```

`gd` と表示されれば有効です。より詳しく見たいときは、次のような確認用ファイルを作ってブラウザで開きます。

```php
<?php
// GD が使えるかどうかと、対応フォーマットを表示する
var_dump(gd_info());
```

> Windows の XAMPP では、`extension=gd` がすでに有効になっていることが多いです。その場合は編集不要です。

---

## GDでの画像処理の基本

`GD` の画像処理は、**「① 読み込む → ② 加工する → ③ 出力する」** の3ステップで考えると分かりやすいです。

### ① 画像を読み込む

読み込み方は、元データの形式によって関数が変わります。

| 関数 | 用途 |
| ---- | ---- |
| `imagecreatefromstring()` | 画像データ（バイト列）から読み込む。形式を自動判別 |
| `imagecreatefromjpeg()` | JPEG ファイルから読み込む |
| `imagecreatefrompng()` | PNG ファイルから読み込む |
| `imagecreatetruecolor()` | 中身が空の新しい画像を作る（加工先の土台） |

アップロードされたファイルは中身を読めば形式が分からなくても扱えるため、`imagecreatefromstring()` が便利です。

```php
// アップロードされた一時ファイルの中身を読み込む
$data = file_get_contents($_FILES['image']['tmp_name']);
// バイト列から画像リソースを作る（JPEG / PNG などを自動判別）
$src = imagecreatefromstring($data);
```

### ② 画像の大きさを調べる

```php
$width  = imagesx($src); // 幅（ピクセル）
$height = imagesy($src); // 高さ（ピクセル）
```

### ③ 画像を出力する

出力する前に、**「これから画像を送る」というヘッダー**を必ず先に送ります。

```php
// ブラウザに「これは PNG 画像です」と伝える
header('Content-Type: image/png');
// 画像本体を出力する
imagepng($pixelated);
// これ以降は何も出力しない
exit;
```

| 関数 | 出力形式 |
| ---- | ---- |
| `imagepng()` | PNG |
| `imagejpeg()` | JPEG |
| `imagegif()` | GIF |

> `header()` より前に HTML や空白・改行を1文字でも出力すると「画像が壊れる」「文字化けする」といったエラーになります。画像を返すファイルでは、`<?php` の前に空行を入れないよう注意します。

### 拡大・縮小に使う関数

| 関数 | 特徴 |
| ---- | ---- |
| `imagecopyresampled()` | ピクセルを補間してなめらかに拡大縮小する（きれい・少し遅い） |
| `imagecopyresized()` | 補間せずそのまま拡大縮小する（粗い・速い） |

この2つの違いが、次のモザイクサンプルの肝になります。

---

## サンプル1: モザイク（ピクセル風）画像を作る

1つ目のサンプル `mosic` は、アップロードした画像を**モザイク（ピクセルアート）風**に変換します。GD だけで作れるので、追加ライブラリは不要です。

### モザイク処理の考え方

モザイクとは、**画像を大きな四角のブロックに区切り、それぞれを1色で塗りつぶした状態**のことです。

これを一から計算するのは大変ですが、GD では次の裏技で簡単に作れます。

1. 画像を**うんと小さく縮小**する（このとき近くの色が1ピクセルにまとめられる）
2. それを**元のサイズまで拡大**する。ただし**補間せずに**拡大する

縮小で色がまとまり、補間なしの拡大で1ピクセルが大きな四角として引き伸ばされるため、結果的にモザイクになります。

### 縮小サイズの計算

`pixelSize`（モザイクの粗さ）で元の幅・高さを割って、縮小後のサイズを求めます。

```php
// モザイクの粗さ。未入力なら 20
$pixelSize = intval($_POST['pixel']) ?: 20;

// 縮小後のサイズ（粗さで割る）
$smallW = intval($width / $pixelSize);
$smallH = intval($height / $pixelSize);
```

`pixelSize` が大きいほど縮小後が小さくなり、モザイクは粗くなります。

### 縮小してから拡大する

```php
// 1. 縮小用の小さな土台を作り、なめらかに縮小コピー
$small = imagecreatetruecolor($smallW, $smallH);
imagecopyresampled($small, $src, 0, 0, 0, 0, $smallW, $smallH, $width, $height);

// 2. 元サイズの土台を作り、補間せずに拡大コピー（ここでモザイクになる）
$pixelated = imagecreatetruecolor($width, $height);
imagecopyresized($pixelated, $small, 0, 0, 0, 0, $width, $height, $smallW, $smallH);
```

ポイントは **縮小は `imagecopyresampled()`、拡大は `imagecopyresized()`** を使い分けているところです。拡大側で `imagecopyresized()`（補間なし）を使うことで、四角いブロックがくっきり出ます。

`imagecopyresampled()` / `imagecopyresized()` の引数は共通で、次の順番です。

| 引数 | 意味 |
| ---- | ---- |
| 第1 | コピー先の画像 |
| 第2 | コピー元の画像 |
| 第3・4 | コピー先の左上座標（x, y） |
| 第5・6 | コピー元の左上座標（x, y） |
| 第7・8 | コピー先での幅・高さ |
| 第9・10 | コピー元から使う範囲の幅・高さ |

### プログラムの流れ

`mosic/index.php` は、**POST でファイルが送られてきたら画像を返し、それ以外はフォームを表示する**という作りです。

```php
<?php
// フォームから画像が POST されたときだけ実行
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image']['tmp_name'];
    // モザイクの粗さ（未入力なら 20）
    $pixelSize = intval($_POST['pixel']) ?: 20;

    if (!file_exists($file)) {
        die('ファイルが見つかりません。');
    }

    // アップロード画像を読み込む
    $upload_file = file_get_contents($file);
    $src = imagecreatefromstring($upload_file);
    $width  = imagesx($src);
    $height = imagesy($src);

    // 縮小後のサイズを計算
    $smallW = intval($width / $pixelSize);
    $smallH = intval($height / $pixelSize);

    // いったん縮小（なめらかに）
    $small = imagecreatetruecolor($smallW, $smallH);
    imagecopyresampled($small, $src, 0, 0, 0, 0, $smallW, $smallH, $width, $height);

    // 元サイズへ拡大（補間なし＝モザイクになる）
    $pixelated = imagecreatetruecolor($width, $height);
    imagecopyresized($pixelated, $small, 0, 0, 0, 0, $width, $height, $smallW, $smallH);

    // PNG 画像として出力
    header('Content-Type: image/png');
    imagepng($pixelated);
    exit;
}
?>
```

HTML 側は、`enctype="multipart/form-data"` を付けた `POST` フォームでファイルを送ります。ファイル送信では、この `enctype` の指定が必須です。

```html
<form method="post" enctype="multipart/form-data">
    <input type="file" name="image" accept="image/*" required>
    <input type="range" name="pixel" value="20" min="2" max="100">
    <button type="submit">ピクセル化する</button>
</form>
```

### 動作確認

<img src="/storage/teaching_material/php_gd_mosic.png" class="" width="500">

処理の流れは次のとおりです。

1. フォームで画像とモザイクの粗さを送る
2. 画像を読み込み、粗さで割ったサイズに縮小する
3. 元サイズへ補間なしで拡大し、モザイクを作る
4. `header()` で PNG を宣言し、`imagepng()` で出力する

> このサンプルは POST の結果として画像そのものを返すため、変換後の画像はページ内に表示されず、ブラウザに直接開かれます。ページ内に並べて表示したい場合は、次の QR コードサンプルのように `<img>` の `src` に処理用ファイルを指定する形にします。

---

## サンプル2: QRコードを生成する

2つ目のサンプル `qrcode` は、入力した URL から **QR コード画像**を生成します。QR コードの計算は複雑なので、`endroid/qr-code` というライブラリに任せます。

### endroid/qr-codeのインストール

```bash
composer require endroid/qr-code
```

`Composer` を使うので、`vendor/autoload.php`（このプロジェクトでは共通の `bootstrap.php`）の読み込みが必要です。

### ライブラリの役割

| 項目 | 内容 |
| ---- | ---- |
| endroid/qr-code | 文字列から QR コード画像を生成する PHP ライブラリ |
| QrCode | 「何を・どのくらいの大きさで」QR 化するかを表すクラス |
| PngWriter | QrCode を PNG 画像データに変換するクラス |

### 画像をページ内に表示する仕組み

QR コードもモザイクと同じく「画像を返すファイル」ですが、今回は**フォームのページ（`index.php`）と画像を返すファイル（`qrcode.php`）を分けています**。

`index.php` 側では、`<img>` タグの `src` に `qrcode.php` を指定します。ブラウザは `src` の URL に自動でアクセスするため、画像がページ内に表示されます。

```html
<!-- src に指定した qrcode.php が画像を返す -->
<img src="qrcode.php?url=https://example.com" alt="QRコード">
```

こうすると、フォームの結果を**ページのレイアウトの中に**表示できます。

### QrCodeクラスの使い方

`QrCode` を作り、`PngWriter` で画像データに変換します。

```php
// QR 化したい文字列や設定を渡して QrCode を作る
$qrCode = new QrCode(
    data: $text,                      // QR に埋め込む文字列（URL など）
    encoding: new Encoding('UTF-8'),  // 文字コード
    size: 300,                        // 画像のサイズ（ピクセル）
    margin: 10,                       // 周囲の余白
);

// PNG 画像データに変換する
$writer = new PngWriter();
$result = $writer->write($qrCode);
```

`data:` `size:` のように**引数名を指定して渡す書き方**（名前付き引数）は PHP 8.0 以降で使えます。順番を気にせず、必要なものだけ渡せます。

### プログラムの流れ（qrcode.php）

`qrcode.php` は QR コード画像だけを返します。

```php
<?php
// 共通の初期化ファイルを読み込む（Composer オートローダーなど）
require_once dirname(__DIR__, 2) . '/bootstrap.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;

$size = 300;
$margin = 10;

// URL を受け取る。無ければエラーを返す
$text = $_GET['url'] ?? '';
if (!$text) {
    http_response_code(400);
    echo 'URLが指定されていません。';
    exit;
}

// QR コードを生成
$qrCode = new QrCode(
    data: $text,
    encoding: new Encoding('UTF-8'),
    size: $size,
    margin: $margin,
);

// PNG に変換
$writer = new PngWriter();
$result = $writer->write($qrCode);

// 画像として出力
header('Content-Type: ' . $result->getMimeType());
echo $result->getString();
```

### フォーム側（index.php）

`index.php` は、URL の入力フォームと、生成結果の `<img>` を表示します。

```php
<?php
// GET パラメータから URL を取得
$url = trim($_GET['url'] ?? '');
// 保存時のファイル名（URL ごとに変わるよう md5 を利用）
$filename = 'qr_' . md5($url) . '.png';
?>
```

```php
<!-- 入力フォーム（method="get" なので URL に ?url=... が付く） -->
<form method="get">
    <input type="text" name="url" value="<?= htmlspecialchars($url) ?>" required>
    <button type="submit">生成</button>
</form>

<?php if ($url !== ''): ?>
    <!-- URL が入力されていれば、その URL の QR コードを表示 -->
    <img id="qrImage" src="qrcode.php?url=<?= urlencode($url) ?>" alt="QRコード">
    <button id="downloadBtn">QR コードを保存</button>
<?php endif; ?>
```

`<?= htmlspecialchars($url) ?>` は、入力値をそのまま HTML に出すときの**エスケープ**です。`urlencode($url)` は、URL の一部として渡すときに記号を安全な形へ変換します。用途によって使い分けます。

保存ボタンは、JavaScript で画像を取得して**ダウンロードとして保存**させています。

```javascript
// 画像を取得して、ファイルとして保存させる
fetch(img.src)
    .then(res => res.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = '<?= $filename ?>'; // 保存ファイル名
        a.click();
        window.URL.revokeObjectURL(url);
    });
```

### 動作確認

<img src="/storage/teaching_material/php_gd_qrcode.png" class="" width="500">

処理の流れは次のとおりです。

1. フォームに URL を入力して送信する（`?url=...` が付く）
2. `index.php` が `<img src="qrcode.php?url=...">` を出力する
3. ブラウザが `qrcode.php` にアクセスし、`endroid/qr-code` が QR 画像を返す
4. 「保存」ボタンで画像をダウンロードできる

> QR コードに埋め込めるのは URL だけではありません。プレーンな文字列、メールアドレス、電話番号なども埋め込めます。読み取り側のアプリがその内容をどう扱うかで動作が変わります。

---

## まとめ

| 項目 | 内容 |
| ---- | ---- |
| GD | PHP で画像を生成・加工するライブラリ（`extension=gd` で有効化） |
| imagecreatefromstring() | バイト列から画像を読み込む（形式を自動判別） |
| imagecreatetruecolor() | 加工先となる空の画像を作る |
| imagecopyresampled() | なめらかに拡大縮小する（縮小向き） |
| imagecopyresized() | 補間せず拡大縮小する（モザイクを作れる） |
| header('Content-Type: image/png') | 画像を返す前に必ず送るヘッダー |
| endroid/qr-code | 文字列から QR コード画像を生成するライブラリ |
| `<img src="処理用.php">` | 画像を返すファイルをページ内に埋め込む方法 |
