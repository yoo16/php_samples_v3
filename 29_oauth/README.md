## ライブラリインストール
### .env
```bash
composer require vlucas/phpdotenv
```

## OAuth(Google)
```bash
composer require league/oauth2-google
```

### Google API Console で認証情報を取得
1. アクセス
https://console.cloud.google.com/

2. OAuth2 クライアントIDを作成（リダイレクトURIを指定）

3. client_id と client_secret を控えておく