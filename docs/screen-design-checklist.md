# 画面実装チェックシート

主要ルートと Blade の見た目・挙動を確認するためのチェックリストです。

## 主要ルート（一般・管理者）

- [ ] `/register` 会員登録画面（一般）
- [ ] `/login` ログイン画面（一般）
- [ ] `/attendance` 勤怠登録画面（一般）
- [ ] `/attendance/list` 勤怠一覧画面（一般）
- [ ] `/attendance/detail/{id}` 勤怠詳細画面（一般）
- [ ] `/stamp_correction_request/list` 申請一覧画面（一般）
- [ ] `/admin/login` ログイン画面（管理者）
- [ ] `/admin/attendance/list` 勤怠一覧画面（管理者）
- [ ] `/admin/attendance/{id}` 勤怠詳細画面（管理者）
- [ ] `/admin/staff/list` スタッフ一覧画面（管理者）
- [ ] `/admin/attendance/staff/{id}` スタッフ別勤怠一覧（管理者）
- [ ] `/stamp_correction_request/list` 申請一覧（管理者）
- [ ] `/stamp_correction_request/approve/{attendance_correct_request_id}` 修正申請承認画面（管理者）

## 画面別確認観点

各画面で下記を確認する。

- [ ] URL が `docs/basic-design.md` のルート概要と一致する
- [ ] 認証/権限でアクセス制御が正しい
- [ ] 主要見出し（h1）が正しい
- [ ] 主要操作（ボタン/リンク）が存在する
- [ ] エラー/成功メッセージが表示される

## デザイン（Figma・素材）

- [ ] Figma 指定のタイポグラフィ（文字サイズ/太さ/行間）を反映
- [ ] 余白とレイアウトグリッドを反映
- [ ] ボタン/入力/テーブルの形状・色を統一
- [ ] 指定素材（ロゴ・画像）を利用
- [ ] インライン style を CSS クラスへ移行

## レスポンシブ（1400–1540px）

- [ ] 画面幅 1540px で崩れない
- [ ] 画面幅 1400px で崩れない
- [ ] テーブル列が重ならない
- [ ] ヘッダーリンクが崩れない
- [ ] 主要フォームが横にはみ出さない

## 検証ログ

| 日時 | 実施者 | 画面 | 結果 | 備考 |
|------|--------|------|------|------|
| YYYY-MM-DD HH:mm |  | （画面名またはパス） | OK/NG |  |
