# COACHTECH 勤怠管理アプリ

Laravel 12 + Laravel Fortify を用いた勤怠管理アプリケーションです。

## ログイン情報（Seeder 実行後）

| 種別 | メールアドレス | パスワード |
|------|----------------|------------|
| 一般ユーザー | `user@example.com` | `password` |
| 管理者 | `admin@example.com` | `password` |

## Docker での起動

リポジトリルートで:

```bash
docker compose up -d --build
```

`src/.env` を用意し、コンテナ内の MySQL に接続する設定にします（例）。

```env
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

# メール確認（MailHog）
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

コンテナ `php` 内でマイグレーションとシード:

```bash
docker compose exec php bash -lc "cd /var/www && composer install && php artisan key:generate && php artisan migrate --force && php artisan db:seed --force"
```

フロントエンドアセット（Vite）をビルド:

```bash
cd src
npm install
npm run build
```

`Illuminate\\Foundation\\ViteManifestNotFoundException` が出る場合は、
`src/public/build/manifest.json` が未生成です。上記コマンドを実行してください。

ホストに `npm` がない場合は、Node コンテナ経由でも実行できます:

```bash
docker run --rm -v "$PWD/src:/app" -w /app node:20 bash -lc "npm install && npm run build"
```

ブラウザ: `http://localhost`（nginx）

- MailHog（開発用メール受信）: `http://localhost:8025`
- phpMyAdmin: `http://localhost:8080`

## ローカル（PHP / Composer あり）の場合

```bash
cd src
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan serve
```

## テスト

```bash
cd src
php artisan test
```

- **用語の整理**（例: 「勤務中」と「出勤中」）: [docs/glossary.md](docs/glossary.md)
- **要件シート「テストケース一覧」↔ PHPUnit の対応表**: [docs/test-traceability.md](docs/test-traceability.md)

## 主な画面パス

| パス | 説明 |
|------|------|
| `/register` | 会員登録（一般） |
| `/login` | ログイン（一般） |
| `/attendance` | 勤怠打刻 |
| `/attendance/list` | 勤怠一覧（月次） |
| `/attendance/detail/{id}` | 勤怠詳細・修正申請 |
| `/stamp_correction_request/list` | 申請一覧（一般 / 管理者で表示切替） |
| `/admin/login` | 管理者ログイン |
| `/admin/attendance/list` | 日次勤怠一覧 |
| `/admin/staff/list` | スタッフ一覧 |
| `/admin/attendance/staff/{id}/export/csv` | スタッフ別月次勤怠 CSV（`year` / `month` クエリ） |
| `/stamp_correction_request/approve/{id}` | 修正申請承認（管理者） |

## ドキュメント

- [基本設計（ルート一覧）](docs/basic-design.md)
- [テーブル仕様・ER](docs/table-spec.md)
- [用語・表記（glossary）](docs/glossary.md)
- [テストトレーサビリティ](docs/test-traceability.md)
- [Figma・素材（DG01）と実装の関係](docs/figma-alignment.md)
