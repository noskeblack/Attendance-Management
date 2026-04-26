# テーブル仕様（概要）

本ドキュメントはアプリ実装と一致するよう整理したテーブル概要です（10 テーブル以内）。

## ER 図

リレーションに加え、**主キー（PK）・外部キー（FK）・主要カラム**を ER 図上に載せ、下記「テーブルごとの列一覧」と対応しやすくしています。型・制約の詳細は各テーブル節の表を正とします。

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email
        timestamp email_verified_at
        string password
        text two_factor_secret
        text two_factor_recovery_codes
        timestamp two_factor_confirmed_at
        boolean is_admin
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    attendances {
        bigint id PK
        bigint user_id FK
        date work_date
        datetime clock_in_at
        datetime clock_out_at
        string status
        text note
        timestamp created_at
        timestamp updated_at
    }

    attendance_breaks {
        bigint id PK
        bigint attendance_id FK
        datetime break_start_at
        datetime break_end_at
        timestamp created_at
        timestamp updated_at
    }

    stamp_correction_requests {
        bigint id PK
        bigint user_id FK
        bigint attendance_id FK
        string status
        text remark
        datetime requested_clock_in_at
        datetime requested_clock_out_at
        json requested_breaks
        timestamp approved_at
        timestamp created_at
        timestamp updated_at
    }

    users ||--o{ attendances : has
    users ||--o{ stamp_correction_requests : requests
    attendances ||--o{ attendance_breaks : has
    attendances ||--o{ stamp_correction_requests : target
```

- `users.email` は DB 上ユニーク（下表参照）。`attendances` は `(user_id, work_date)` の複合ユニーク。

## users

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint | PK |
| name | string | 氏名 |
| email | string | 一意 |
| email_verified_at | timestamp | メール認証日時 |
| password | string | ハッシュ |
| two_factor_secret | text | 任意（Fortify 2FA） |
| two_factor_recovery_codes | text | 任意 |
| two_factor_confirmed_at | timestamp | 2FA 確認日時 |
| is_admin | boolean | 管理者フラグ |
| remember_token | string | 任意 |
| created_at / updated_at | timestamp | |

## attendances

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint | PK |
| user_id | FK users | |
| work_date | date | 日付（ユーザー×日付で一意） |
| clock_in_at | datetime | 出勤 |
| clock_out_at | datetime | 退勤 |
| status | string | off_duty / working / on_break / completed |
| note | text | 備考 |
| created_at / updated_at | timestamp | |

## attendance_breaks

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint | PK |
| attendance_id | FK attendances | |
| break_start_at | datetime | |
| break_end_at | datetime | 休憩中は null |
| created_at / updated_at | timestamp | |

## stamp_correction_requests

| カラム | 型 | 説明 |
|--------|-----|------|
| id | bigint | PK |
| user_id | FK users | 申請者 |
| attendance_id | FK attendances | 対象勤怠 |
| status | string | pending / approved |
| remark | text | 備考 |
| requested_clock_in_at | datetime | 申請時刻 |
| requested_clock_out_at | datetime | |
| requested_breaks | json | 休憩配列 |
| approved_at | timestamp | 承認日時 |
| created_at / updated_at | timestamp | |

## その他

Laravel 標準の `sessions`, `cache`, `jobs`, `password_reset_tokens` などはフレームワーク用です。
