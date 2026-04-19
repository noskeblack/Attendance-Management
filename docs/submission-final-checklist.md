# 提出用 最終チェックリスト

このチェックリストは、提出前に環境・機能・画面ルート・開発品質・テストを一通り確認するためのものです。

## 0. 事前準備

- [ ] `docker compose up -d --build` でコンテナが起動する
- [ ] `src/.env` が Docker 構成（MySQL / MailHog）になっている
- [ ] `docker compose exec php bash -lc "cd /var/www && php artisan migrate --force && php artisan db:seed --force"` が成功する
- [ ] `README.md` に一般 / 管理者ログイン情報が記載されている

## 1. 機能要件（主要）

- [ ] Fortify で一般ユーザー登録 / ログイン / ログアウトができる
- [ ] Fortify で管理者ログイン / ログアウトができる（一般ユーザーと経路を分離）
- [ ] メール認証導線（認証誘導 / 認証リンク / 再送）が動作する
- [ ] 勤怠打刻（出勤 / 休憩入 / 休憩戻 / 退勤）が仕様どおり動く
- [ ] 一般ユーザーの勤怠一覧 / 勤怠詳細 / 修正申請が動く
- [ ] 管理者の日次一覧 / 勤怠詳細修正 / スタッフ一覧 / スタッフ別月次が動く
- [ ] 申請一覧（一般 / 管理者）が表示切替される
- [ ] 管理者承認で、申請が承認待ち→承認済みに移動し、勤怠に反映される
- [ ] 承認待ち申請がある勤怠は、一般ユーザー・管理者とも編集不可になる
- [ ] スタッフ別月次の CSV 出力ができる（応用要件 FN045）

## 2. 主要 Web 画面（ルート）

- [ ] `/register`
- [ ] `/login`
- [ ] `/attendance`
- [ ] `/attendance/list`
- [ ] `/attendance/detail/{id}`
- [ ] `/stamp_correction_request/list`（一般 / 管理者で表示切替）
- [ ] `/admin/login`
- [ ] `/admin/attendance/list`
- [ ] `/admin/attendance/{id}`
- [ ] `/admin/staff/list`
- [ ] `/admin/attendance/staff/{id}`
- [ ] `/stamp_correction_request/approve/{id}`

## 3. 開発プロセス

- [ ] マイグレーションとテーブル仕様の整合が取れている
- [ ] Seeder で管理者 / 一般ユーザー / 勤怠（出勤・退勤・休憩）ダミーデータが作られる
- [ ] 命名規約（クラス名・ファイル名）が崩れていない
- [ ] 不要な `use` や過剰コメントアウトが残っていない
- [ ] 指定技術（Fortify / FormRequest / PHPUnit）を使用している

## 4. テストケース一覧対応

- [ ] `docker compose exec php bash -lc "cd /var/www && php artisan test"` が全件成功する
- [ ] 認証・打刻・一覧/詳細・修正申請・管理者機能・メール認証の Feature テストがある
- [ ] 文言系バリデーションが要件どおり（特に FN003 / FN009 / FN016 / FN029）
- [ ] 「全て表示」系（承認待ち / 承認済み / 全ユーザー）のテストがある
- [ ] 「詳細」リンク遷移の検証がある

## 5. 参考ドキュメント

- ルート概要: `docs/basic-design.md`
- テーブル仕様: `docs/table-spec.md`
- テスト対応表: `docs/test-traceability.md`
