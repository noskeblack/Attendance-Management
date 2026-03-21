# 基本設計（ルート概要）

アプリ本体は `src/` 配下の Laravel プロジェクトです。

## Web ルート（一般ユーザー・認証必須）

| 画面 | メソッド | パス | コントローラ |
|------|----------|------|--------------|
| 勤怠打刻 | GET | `/attendance` | `AttendanceController@index` |
| 出勤 | POST | `/attendance/clock-in` | `AttendanceController@clockIn` |
| 退勤 | POST | `/attendance/clock-out` | `AttendanceController@clockOut` |
| 休憩入 | POST | `/attendance/break-start` | `AttendanceController@breakStart` |
| 休憩戻 | POST | `/attendance/break-end` | `AttendanceController@breakEnd` |
| 勤怠一覧 | GET | `/attendance/list` | `AttendanceListController@index` |
| 勤怠詳細 | GET | `/attendance/detail/{attendance}` | `AttendanceDetailController@show` |
| 修正申請 | POST | `/attendance/detail/{attendance}` | `AttendanceDetailController@submitCorrection` |
| 申請一覧 | GET | `/stamp_correction_request/list` | `StampCorrectionRequestController@index` |

認証・会員登録・メール認証・パスワード再設定は **Laravel Fortify** が提供します（`/login`, `/register`, `/email/verify` など）。

## Web ルート（管理者）

| 画面 | メソッド | パス | コントローラ |
|------|----------|------|--------------|
| 管理者ログイン | GET/POST | `/admin/login` | `Admin\AdminLoginController@create` / `Fortify AuthenticatedSessionController@store` |
| 日次勤怠一覧 | GET | `/admin/attendance/list` | `Admin\AdminAttendanceController@daily` |
| 勤怠詳細 | GET/PUT | `/admin/attendance/{attendance}` | `Admin\AdminAttendanceController@show` / `update` |
| スタッフ一覧 | GET | `/admin/staff/list` | `Admin\AdminStaffController@index` |
| スタッフ別勤怠一覧 | GET | `/admin/attendance/staff/{user}` | `Admin\AdminStaffController@monthlyAttendance` |
| スタッフ別月次 CSV | GET | `/admin/attendance/staff/{user}/export/csv` | `Admin\AdminStaffController@exportMonthlyCsv` |
| 修正申請承認 | GET/POST | `/stamp_correction_request/approve/{stamp_correction_request}` | `Admin\StampCorrectionApproveController` |

## ミドルウェア

- 一般ユーザー画面: `auth`, `verified`, `not_admin`（管理者は一般ユーザー向け画面へ入ると管理者画面へリダイレクト）
- 管理者画面: `auth`, `verified`, `admin`
- 申請一覧 `/stamp_correction_request/list`: 一般・管理者共通（コントローラ内で表示を切替）
