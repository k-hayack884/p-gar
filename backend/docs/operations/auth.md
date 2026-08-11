# 認証運用手順

## 初期ユーザーの設定

`backend/.env` に管理者のメールアドレスとパスワードを設定します。

```dotenv
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=<strong-password>
```

`ADMIN_PASSWORD` には運用環境ごとに異なる十分に強い値を使用し、実際の値をリポジトリへコミットしないでください。設定後、`backend` ディレクトリで次のコマンドを実行します。

```bash
php artisan db:seed --class=UserSeeder
```

Seeder は `ADMIN_EMAIL` のユーザーが存在しない場合にのみ作成します。既存ユーザーのパスワードは更新しません。

## ログインと API

フロントエンドのログイン URL は `/login` です。認証で使用するエンドポイントは次のとおりです。

| メソッド | エンドポイント | 用途 |
| --- | --- | --- |
| `GET` | `/sanctum/csrf-cookie` | ログイン前の CSRF Cookie 取得 |
| `POST` | `/api/auth/login` | ログイン |
| `POST` | `/api/auth/logout` | ログアウト |
| `GET` | `/api/auth/me` | ログイン中のユーザー取得 |

## パスワードの変更

`backend` ディレクトリで `php artisan tinker` を起動し、対象ユーザー ID と新しいパスワードを指定します。

```php
User::find(1)->update(['password' => bcrypt('new')]);
```

変更後は旧パスワードが無効であることと、新パスワードでログインできることを確認します。
