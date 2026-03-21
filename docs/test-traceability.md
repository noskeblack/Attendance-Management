# テストケース一覧 ↔ PHPUnit トレーサビリティ

要件シート **「テストケース一覧」** の行（Excel上の行番号）と、対応する **Feature テスト** の目安です。  
「1行＝1メソッド」ではなく、**近い責務のテストに集約**している行もあります。

---

## 凡例

| 列 | 意味 |
|----|------|
| Excel行 | シート上の行番号 |
| PHPUnit | `tests/Feature/` 以下の `クラス::メソッド` |
| 備考 | 表記ゆれ・補足 |

---

## ID 1 — 認証機能（一般ユーザー） (Excel 5–10)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 5 | `RegisterValidationTest::test_register_requires_name` | |
| 6 | `RegisterValidationTest::test_register_requires_email` | |
| 7 | `RegisterValidationTest::test_register_requires_password_min_8` | 8文字未満メッセージ |
| 8 | `RegisterValidationTest::test_register_requires_password_confirmation_match` | |
| 9 | `RegisterValidationTest::test_register_requires_password_when_missing`, `test_register_requires_password_when_empty` | 未入力 |
| 10 | `RegisterValidationTest::test_register_persists_user_to_database`, `EmailVerificationFlowTest::test_register_sends_verification_notification` | 保存＋通知 |

---

## ID 2.0 — ログイン（一般） (11–13)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 11 | `LoginValidationTest::test_login_requires_email` | |
| 12 | `LoginValidationTest::test_login_requires_password` | |
| 13 | `LoginValidationTest::test_login_fails_with_invalid_credentials` | |

---

## ID 3.0 — ログイン（管理者） (14–16)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 14 | `AdminLoginValidationTest::test_admin_login_requires_email` | |
| 15 | `AdminLoginValidationTest::test_admin_login_requires_password` | |
| 16 | `AdminLoginValidationTest::test_admin_login_rejects_general_user` | 不一致／権限 |

---

## ID 4.0 — 日時取得 (17)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 17 | `AttendanceScreenTest::test_attendance_screen_shows_current_datetime_in_expected_format` | |

---

## ID 5.0 — ステータス確認 (18–21)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 18–21 | `AttendanceScreenTest::test_attendance_screen_shows_status_labels` | 勤務外／出勤中／休憩中／退勤済 |

---

## ID 6.0 — 出勤 (22–24)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 22 | `AttendanceStampExtendedTest::test_clock_in_changes_status_to_working_and_shows_clock_in_button_only_once` | シートの「勤務中」⇔実装「出勤中」は [glossary.md](glossary.md) |
| 23 | 同上 + `AttendanceStampTest::test_user_cannot_clock_in_twice_in_a_day` | |
| 24 | `AttendanceStampExtendedTest::test_clock_out_changes_status_to_completed_and_list_shows_clock_out` 等 + 一覧表示 | |

---

## ID 7.0 — 休憩 (25–29)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 25–29 | `AttendanceStampExtendedTest::test_break_flow_and_list_times` | 複数回休憩・一覧 |

---

## ID 8.0 — 退勤 (30–31)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 30–31 | `AttendanceStampExtendedTest::test_clock_out_changes_status_to_completed_and_list_shows_clock_out` | |

---

## ID 9.0 — 勤怠一覧（一般） (32–36)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 32–35 | `AttendanceListAndDetailTest::test_list_shows_only_own_attendance_and_supports_month_navigation_and_detail_link`, `AttendanceListNavigationTest::*` | 自分のみ・月移動 |
| 36 | 同上（`href` で詳細URL確認） | |

---

## ID 10.0 — 勤怠詳細（一般） (37–40)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 37–40 | `AttendanceListAndDetailTest::*` | 名前・日付・時刻・休憩 |

---

## ID 11.0 — 勤怠修正（一般） (41–48)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 41–44 | `AttendanceCorrectionValidationTest::*` | |
| 45–47 | `StampCorrectionFlowTest::*`, `StampCorrectionRequestExtendedTest::*`, `StampCorrectionRequestListCoverageTest::*` | 申請・一覧の網羅 |
| 48 | `StampCorrectionRequestListCoverageTest::test_user_request_detail_links_point_to_attendance_detail` | 詳細リンク |

---

## ID 12.0 — 勤怠一覧（管理者・日次） (49–52)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 49–52 | `AdminScreensTest::test_admin_daily_list_shows_all_users_and_accurate_times_and_date_navigation`, `AdminDailyAttendanceNavigationTest::*` | |

---

## ID 13.0 — 勤怠詳細・修正（管理者） (53–57)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 53 | `AdminScreensTest::test_admin_attendance_detail_shows_selected_user_and_date` | |
| 54–57 | `AdminAttendanceValidationTest::*` | |
| （FN038・承認待ち） | `AdminAttendancePendingLockTest::test_admin_cannot_edit_attendance_when_correction_request_is_pending` | 編集ロック |

---

## ID 14.0 — スタッフ情報（管理者） (58–62)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 58–61 | `AdminScreensTest::test_admin_staff_list_shows_names_and_emails_and_monthly_navigation` | |
| 62 | 同上（`href` で管理者勤怠詳細） | |
| （FN045・応用） | `AdminMonthlyAttendanceCsvTest::test_admin_can_download_monthly_attendance_csv` | スタッフ別月次 CSV |

---

## ID 15.0 — 修正申請（管理者） (63–66)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 63–64 | `StampCorrectionRequestListCoverageTest::test_admin_sees_pending_requests_from_all_users`, `test_admin_sees_approved_requests_from_all_users` | 全ユーザー横断 |
| 65 | `StampCorrectionRequestListCoverageTest::test_admin_approve_screen_displays_request_payload` | 申請内容表示 |
| 66 | `AdminScreensTest::test_admin_correction_request_lists_and_approve_updates_attendance` | 承認と勤怠更新 |

---

## ID 16.0 — メール認証 (71–73)

| Excel行 | PHPUnit | 備考 |
|--------|---------|------|
| 71 | `EmailVerificationFlowTest::test_register_sends_verification_notification` | |
| 72 | `EmailVerificationFlowTest::test_verify_email_notice_contains_signed_verification_link_button` | |
| 73 | `EmailVerificationFlowTest::test_visiting_signed_verification_url_verifies_and_redirects_to_attendance` | 遷移先名称は [glossary.md](glossary.md) |

---

## その他

| PHPUnit | 備考 |
|---------|------|
| `ExampleTest::test_root_redirects_to_login` | ルート仕様 |
| `AttendanceStampTest::test_user_can_clock_in_break_and_clock_out` | 基本打刻フロー統合 |
