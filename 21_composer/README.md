## PHPパス設定
1. php コマンドのパスを環境変数に登録

## Composer インストール
1. Composer公式サイトのダウンロードページ から Composer-Setup.exe をダウンロード
2. ダウンロードしたファイルを実行
3. パスを通した php.exe が正しく選択されていることを確認して進める。
4. プロキシは、通常の個人環境であれば何も入力せずに「Next」で進める。
5. 「Install」をクリック

## Composer 確認
1. VSCode で、このプロジェクトフォルダを開く
2. ターミナルを開く
3. composer コマンドで、バージョン確認

```bash
composer -V
```

## ライブラリインストール
### .env
```bash
composer require vlucas/phpdotenv
```