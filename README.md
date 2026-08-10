# p-gar

**p-gar** は、家庭菜園の作業記録・タスク・栽培データを一元管理し、日々の菜園作業を効率化するシステムです。バックエンドに Laravel 11、フロントエンドに Vue 3 / TypeScript / Vite、データベースに MySQL 8 を使用します。

## プロジェクト構成

- `backend/`: Laravel API（PHP 8.3）
- `frontend/`: Vue 3 SPA（Node.js 22）
- `docker/`: nginx、PHP-FPM、MySQL、Node.js の Docker 定義
- `firmware/`: Phase 2 向け ESP32 ファームウェア
- `docs/`: 要件・設計・運用ドキュメント

## 前提ソフトウェア

ローカルの Docker Engine と Docker Compose v2（Docker Desktop を含む）を使用します。Docker を使わずに個別起動する場合は、PHP 8.3、Composer 2、Node.js 22、npm、MySQL 8 以降を用意してください。

## ローカル起動

以下のコマンドはリポジトリルートで実行します。

### 初回セットアップ

1. バックエンド用の環境ファイルを作成します。

   ```sh
   cp backend/.env.example backend/.env
   ```

2. Docker イメージをビルドし、nginx、PHP-FPM、MySQL、Node.js を起動します。

   ```sh
   docker compose up -d --build
   ```

3. PHP とフロントエンドの依存関係をインストールします。Node.js サービスは起動時にも `npm install` を実行しますが、初回セットアップでは明示的に実行します。

   ```sh
   docker compose exec php composer install --no-interaction --no-progress --prefer-dist
   docker compose exec node npm install
   ```

4. アプリケーションキーを生成し、データベースをマイグレーションします。

   ```sh
   docker compose exec php php artisan key:generate --ansi
   docker compose exec php php artisan migrate --graceful --ansi
   ```

ブラウザから次の URL にアクセスします。

- バックエンド: <http://localhost:18080>
- フロントエンド開発サーバー: <http://localhost:15173>

ポートを変更する場合は、リポジトリルートの Compose 用 `.env` で `APP_PORT` または `FRONTEND_PORT` を指定します。データベースのホスト公開ポートは `DB_FORWARD_PORT` で変更できます。起動中のログは `docker compose logs -f` で確認できます。

### 停止・再起動

```sh
docker compose stop
docker compose start
```

コンテナを削除して再作成する場合は、次のコマンドを使用します。名前付きボリュームは削除されないため、MySQL のローカルデータは保持されます。

```sh
docker compose down
docker compose up -d
```

## 環境変数

アプリケーションの環境変数は `backend/.env` で管理します。`backend/.env.example` をコピーしてから、環境に合わせて値を変更してください。認証情報や API キーなどの秘密情報はコミットしないでください。

### `backend/.env.example` の変数

| 変数 | 用途・意味 |
| --- | --- |
| `APP_NAME` | アプリケーション名です。 |
| `APP_ENV` | 実行環境（`local`、`testing`、`production` など）です。 |
| `APP_KEY` | Laravel の暗号化キーです。`php artisan key:generate` で生成します。 |
| `APP_DEBUG` | デバッグ表示の有効・無効です。本番環境では `false` にします。 |
| `APP_TIMEZONE` | アプリケーションの既定タイムゾーンです。 |
| `APP_URL` | アプリケーションの基準 URL です。 |
| `APP_LOCALE` | 既定の表示言語です。 |
| `APP_FALLBACK_LOCALE` | 既定の言語が利用できない場合の代替言語です。 |
| `APP_FAKER_LOCALE` | テストデータ生成に使う Faker のロケールです。 |
| `APP_MAINTENANCE_DRIVER` | メンテナンスモードの保存方式です。 |
| `APP_MAINTENANCE_STORE` | メンテナンスモードの保存先です。コメントを外した場合に使用します。 |
| `PHP_CLI_SERVER_WORKERS` | PHP 組み込み開発サーバーのワーカー数です。 |
| `BCRYPT_ROUNDS` | bcrypt のハッシュ計算コストです。 |
| `LOG_CHANNEL` | 既定のログ出力先です。 |
| `LOG_STACK` | `stack` チャンネルで組み合わせるログチャンネルです。 |
| `LOG_DEPRECATIONS_CHANNEL` | 非推奨機能のログ出力先です。`null` は専用出力を無効にします。 |
| `LOG_LEVEL` | 出力するログの最低レベルです。 |
| `DB_CONNECTION` | データベースドライバー（`sqlite`、`mysql` など）です。 |
| `DB_HOST` | データベースのホスト名です。コメントを外した場合に使用します。 |
| `DB_PORT` | データベースのポート番号です。 |
| `DB_DATABASE` | 接続するデータベース名です。 |
| `DB_USERNAME` | データベースのユーザー名です。 |
| `DB_PASSWORD` | データベースのパスワードです。 |
| `SESSION_DRIVER` | セッションの保存方式です。 |
| `SESSION_LIFETIME` | セッションの有効期間（分）です。 |
| `SESSION_ENCRYPT` | セッションデータを暗号化するかどうかです。 |
| `SESSION_PATH` | セッション Cookie のパスです。 |
| `SESSION_DOMAIN` | セッション Cookie のドメインです。 |
| `BROADCAST_CONNECTION` | イベントブロードキャストの接続方式です。 |
| `FILESYSTEM_DISK` | 既定のファイル保存ディスクです。 |
| `QUEUE_CONNECTION` | ジョブキューの接続方式です。 |
| `CACHE_STORE` | キャッシュの保存方式です。 |
| `CACHE_PREFIX` | キャッシュキーに付ける接頭辞です。 |
| `MEMCACHED_HOST` | Memcached のホスト名です。 |
| `REDIS_CLIENT` | Redis クライアントです。 |
| `REDIS_HOST` | Redis のホスト名です。 |
| `REDIS_PASSWORD` | Redis のパスワードです。 |
| `REDIS_PORT` | Redis のポート番号です。 |
| `MAIL_MAILER` | メール送信方式です。開発時の `log` は実送信せずログに出力します。 |
| `MAIL_SCHEME` | メール接続のスキームです。 |
| `MAIL_HOST` | SMTP ホスト名です。 |
| `MAIL_PORT` | SMTP ポート番号です。 |
| `MAIL_USERNAME` | SMTP のユーザー名です。 |
| `MAIL_PASSWORD` | SMTP のパスワードです。 |
| `MAIL_FROM_ADDRESS` | 送信元メールアドレスです。 |
| `MAIL_FROM_NAME` | 送信元として表示する名前です。 |
| `AWS_ACCESS_KEY_ID` | S3 互換ストレージのアクセスキーです。 |
| `AWS_SECRET_ACCESS_KEY` | S3 互換ストレージのシークレットキーです。 |
| `AWS_DEFAULT_REGION` | S3 互換ストレージのリージョンです。 |
| `AWS_BUCKET` | 使用する S3 バケット名です。 |
| `AWS_USE_PATH_STYLE_ENDPOINT` | S3 のパス形式エンドポイントを使用するかどうかです。 |
| `VITE_APP_NAME` | Vite がフロントエンドへ渡すアプリケーション名です。 |

### Docker Compose 用の変数

これらは `docker/docker-compose.yml` が参照する変数です。指定しない場合は右側の既定値が使用されます。Compose の変数を変更する場合は、リポジトリルートの `.env` に記載してください。

| 変数 | 既定値 | 用途・意味 |
| --- | --- | --- |
| `COMPOSE_PROJECT_NAME` | `p-gar` | Compose のプロジェクト名です。 |
| `APP_PORT` | `18080` | nginx をホストへ公開するポートです。 |
| `FRONTEND_PORT` | `15173` | Vite 開発サーバーをホストへ公開するポートです。 |
| `DB_FORWARD_PORT` | `13306` | MySQL をホストへ公開するポートです。 |
| `DB_DATABASE` | `p_gar` | MySQL に作成するアプリケーション用データベース名です。 |
| `DB_USERNAME` | `p_gar` | MySQL に作成するアプリケーション用ユーザー名です。 |
| `DB_PASSWORD` | `p_gar_dev` | MySQL アプリケーションユーザーのパスワードです。 |
| `DB_ROOT_PASSWORD` | `root_dev` | MySQL root ユーザーのパスワードです。 |

Compose の `php` サービスは `DB_CONNECTION=mysql`、`DB_HOST=mysql` などをコンテナ用に設定するため、`backend/.env.example` の SQLite 設定は Docker 起動時には上書きされます。ホストからバックエンドを直接起動する場合は、`backend/.env` の `DB_*` をホスト上のデータベースに合わせて設定してください。

## テスト・静的チェック

Docker 環境を起動した状態で、CI と同じチェックをローカルで実行できます。

```sh
docker compose exec php composer install --no-interaction --no-progress --prefer-dist
docker compose exec node npm ci
docker compose exec php php artisan key:generate --ansi
docker compose exec php vendor/bin/phpunit
docker compose exec node npm test
docker compose exec node npm run build
docker compose exec php vendor/bin/pint --test
docker compose exec php vendor/bin/phpstan analyse app --level=5 --no-progress --memory-limit=1G
```

PHPUnit は `backend/phpunit.xml` の設定により `p_gar_test` データベースを使用します。Docker の MySQL 初期化スクリプトがこのテスト用データベースを作成します。初期化済みボリュームを再利用してデータベースが存在しない場合は、MySQL コンテナを再作成するか、テスト用データベースを手動で作成してください。

Docker を使わずに実行する場合は、`backend/` で Composer の依存関係をインストールし、PHPUnit 実行前に `backend/.env` の `DB_*` と `APP_KEY` を設定してください。フロントエンドのチェックは `frontend/` で `npm ci` の後に同じ npm コマンドを実行します。

## CI

`.github/workflows/ci.yml` は `main` または `sprint1` への push と、それらのブランチを対象とする pull request で実行されます。次の4ジョブを実行します。

- **PHPUnit**: MySQL 8.4.11 サービスを起動し、PHP 8.3 と Composer の依存関係を準備した後、Laravel の環境ファイルとアプリケーションキーを準備して `vendor/bin/phpunit` を実行します。
- **Vitest**: Node.js 22 で `npm ci`、`npm test`、`npm run build` を実行します。
- **Laravel Pint**: PHP 8.3 で依存関係を準備し、`vendor/bin/pint --test` で PHP のコーディング規約を検査します。
- **PHPStan**: PHP 8.3 で依存関係を準備し、`vendor/bin/phpstan analyse app --level=5 --no-progress --memory-limit=1G` で静的解析を実行します。

CI と同じ順序で確認する場合は、上記の「テスト・静的チェック」に記載したコマンドを使用してください。

## ライセンス・セキュリティ

秘密情報を含む `.env` はリポジトリへコミットしないでください。脆弱性を発見した場合は、公開 Issue ではなくプロジェクト管理者へ連絡してください。
