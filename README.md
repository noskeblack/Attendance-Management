# COACHTECH 勤怠管理アプリ

Laravel 12 + Laravel Fortify を用いた勤怠管理アプリケーションです。

## ログイン情報（Seeder 実行後）

| 種別 | メールアドレス | パスワード |
|------|----------------|------------|
| 一般ユーザー | `user@example.com` | `password` |
| 管理者 | `admin@example.com` | `password` |

一般ユーザーは `/login`、管理者は `/admin/login` からログインしてください。

## Docker での起動

リポジトリルートで:

```bash
docker compose up -d --build
```

`src/.env` は **`src/.env.example` をコピー**して用意します。`DB_HOST` など環境変数の名前と例は `src/.env.example` にだけ書いてあり、README には同じブロックを載せていません。二か所に同じ内容を書くと、片方だけ古いまま残りやすいためです。

```bash
cp src/.env.example src/.env
```

Docker Compose で起動するときは、**アプリがコンテナ内の MySQL と MailHog に接続するよう** `src/.env` を直します。

1. **データベース（DB）**  
   コピー直後は PC 内のファイルだけ使う **SQLite** 向けの行が有効です。Docker では別コンテナの **MySQL** を使うので、SQLite 側の `DB_CONNECTION` と `DB_DATABASE` の行の先頭に `#` を付けて無効にし、`src/.env.example` 内の「Docker compose 利用時」と書かれた MySQL 用の行から `#` を外して有効にします。

2. **メール（MailHog）**  
   開発中にブラウザで受信テストするため、メールは Docker の **MailHog** に送ります。`src/.env.example` の「Docker + MailHog」と書かれた `MAIL_*` のブロックを有効にし、上にある `MAIL_MAILER=log` などローカル向けの `MAIL_*` 行は `#` でコメントアウトします。

`APP_URL` は、ブラウザのアドレスバーに合わせます（例: `http://localhost`）。

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
touch database/database.sqlite
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
- [テストトレーサビリティ](docs/test-traceability.md)
- [画面設計チェックリスト](docs/screen-design-checklist.md)
- [UIレビュー指摘メモ](docs/ui-review-notes.md)
- [Figma差分調整メモ](docs/figma-alignment.md)
- [提出前最終チェック](docs/submission-final-checklist.md)
- [Blade/CSSリファクタリングテンプレート](docs/blade-css-refactor-template.md)
