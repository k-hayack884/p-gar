# p-gar Claude Guide

## 最初に読むファイル

`AGENTS.md` にプロジェク概要、ディレクトリ構成、実装原則を記載しています。作業前に参照してください。

## 起動・検証の入口

- ローカル起動、環境変数、テスト、CI: `README.md`
- Docker Compose 実体定義: `docker/docker-compose.yml`
- 環境変数の雛形: `.env.example`

## ディレクトリ

- `backend/`: PHP 8.3 / Laravel 11 API
- `frontend/`: Vue 3 / TypeScript / Vite SPA
- `firmware/`: Phase 2 向けファームウェア
- `docs/`: 要件・設計・運用文書
- `docker/`: ローカル実行環境
