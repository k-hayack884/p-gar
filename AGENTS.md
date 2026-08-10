# p-gar Agent Guide

## プロジェクト概要

p-gar は、複数拠点の家庭菜園を対象に、作業記録・タスク・栽培データを一元管理するシステムです。バックエンドは PHP 8.3 / Laravel 11、フロントエンドは Vue 3 / TypeScript / Vite、データベースは MySQL 8 を使用します。

## ディレクトリ構成

- `backend/`: Laravel API。Domain / Application / Infrastructure / Http の層を分離します。
- `frontend/`: Vue 3 SPA。
- `firmware/`: Phase 2 の ESP32 ファームウェア。
- `docker/`: nginx、php-fpm、mysql、node のローカル環境。
- `docs/`: 要件、設計、運用ドキュメント。

## 開発の入口

- 起動手順、環境変数、テスト手順は `README.md` を参照してください。
- Docker Compose の実体定義は `docker/docker-compose.yml` です。
- シークレットをリポジトリに含めず、ローカルの `.env` は `.env.example` から作成します。

## 実装原則

- Controller は薄く保ち、ユースケースを Application 層に配置します。
- Domain 層は可能な限りフレームワークに依存させません。
- 変更に対応するテストを実行し、SKIP が1件でもある場合は完了と判定しません。
