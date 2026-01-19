# coachtech 勤怠管理アプリ
提出タグ: submission-20260116

## 概要
本アプリケーションは一般ユーザーと管理者の2権限を持つ勤怠管理システムです。  
勤怠登録・勤怠一覧・修正申請・承認などの機能を備えています。

## 使用技術（実行環境）
- Laravel 12.x
- PHP 8.2+
- MySQL 8.0
- nginx 1.25

## 機能一覧

### 一般ユーザー
- 勤怠(出勤・休憩・退勤)記録
- 勤怠一覧閲覧（月次／前月・翌月への切り替え）
- 勤怠詳細閲覧
- 勤怠修正申請

### 管理者
- 勤怠一覧閲覧（日次／前日・翌日への切り替え）
- 勤怠詳細閲覧
- スタッフ一覧閲覧
- スタッフ別勤怠一覧閲覧
- 申請一覧閲覧
- 修正申請承認

## 画面一覧

### 一般ユーザー
- `/register`：会員登録
- `/login`：ログイン
- `/attendance`：勤怠登録（出勤・休憩・退勤）
- `/attendance/list`：勤怠一覧（月次）
- `/attendance/detail/{attendance}`：勤怠詳細・修正申請
- `/requests`：申請一覧（承認待ち・承認済み）

### メール認証
- `/register/verify`：メール認証誘導画面
- `/email/verify`：メール認証画面

### 管理者
- `/admin/login`：管理者ログイン
- `/admin/attendance/list`：勤怠一覧（日次）
- `/admin/attendance/detail/{attendance}`：勤怠詳細・修正
- `/admin/staff/list`：スタッフ一覧
- `/admin/staff/{staffId}/attendance`：スタッフ別勤怠一覧
- `/admin/staff/{staffId}/attendance/export`：スタッフ別勤怠一覧CSV出力
- `/admin/requests`：申請一覧
- `/admin/requests/{attendanceCorrection}`：申請詳細・承認

## ER図
![ER図](docs/erd/coachtech-attendance.drawio.png)

## テーブル仕様（抜粋）

本アプリの主要テーブルです。
全カラム網羅ではなく、初見でDBの全体像が把握できる粒度で記載しています。

### users
- 役割:ユーザー情報(一般/管理者)
- 主なカラム:
  - `id` (PK)
  - `name`
  - `email`
  - `email_verified_at` (メール認証)
  - `password`
  - `is_admin` (管理者判定)
  - `remember_token`

### attendances
- 役割：日ごとの勤怠情報（出勤・退勤・備考）
- 主なカラム：
  - `id` (PK)
  - `user_id` (FK → users.id)
  - `work_date`(勤務日)
  - `clock_in_at`(出勤時刻)
  - `clock_out_at`(退勤時刻)
  - `notes`(備考)
- 制約：
  - `user_id` + `work_date` をユニーク(1日1件の勤怠)

### break_times
- 役割：休憩時間(1勤怠に対して複数件)
- 主なカラム：
  - `id` (PK)
  - `attendance_id` (FK → attendances.id)
  - `break_start_at`
  - `break_end_at`

### attendance_requests
- 役割：勤怠修正申請(申請内容・承認状況)
- 主なカラム：
  - `id` (PK)
  - `attendance_id` (FK → attendances.id)
  - `requested_clock_in_at`(修正希望：出勤)
  - `requested_clock_out_at`(修正希望：退勤)
  - `requested_breaks`(修正希望：休憩 ※JSON)
  - `requested_notes`(修正希望：備考)
  - `status`(承認状況：未承認 / 承認済)
  - `reviewed_admin_id` (FK → users.id)
  - `reviewed_at`(承認日時)

※ 詳細は `database/migrations` を参照してください。

## 環境構築手順

### 1. Docker ビルド
```bash
git clone https://github.com/koshikawa-minori/coachtech-attendance
cd coachtech-attendance
docker-compose up -d --build
```

### 2. Laravel 環境構築
```bash
docker-compose exec php bash
composer install
cp .env.example .env  #環境変数を変更
php artisan key:generate
php artisan migrate --seed
```
- DB 接続情報（docker-compose.yml の設定と一致させてください。）
- キャッシュ設定（`.env` の CACHE_DRIVER を `file` に変更してください。）
- `php artisan migrate --seed` 実行時に、管理者・一般ユーザー・勤怠データ・申請データのダミーが作成されます。

## 開発環境URL
- 一般ユーザーログイン: http://localhost/login
- 管理者ログイン: http://localhost/admin/login
- phpMyAdmin: http://localhost:8080/

## テストユーザー情報

| ユーザー種別 | メールアドレス | パスワード |
|---------------|----------------|-------------|
| 一般ユーザー | test@example.com | password |
| 管理ユーザー | admin@example.com | password |

- 本アプリではメール認証を実装しています。
- 上記テストユーザーは、動作確認用としてメール認証済みの状態で作成されています。

## メール認証機能

本アプリでは メール認証機能を実装しており、
Mailtrap などのメールサービスを利用して動作確認できます。

開発環境では、外部メールサービスの送信上限により  
`MAIL_MAILER=log` を利用し、認証メール本文を `storage/logs/laravel.log` に出力して動作確認を行っています。

### メール認証手順

1. `.env` に Mailtrap の `MAIL_USERNAME` / `MAIL_PASSWORD` を設定
2. `/register`（会員登録画面）で新規登録を行います。
3. 登録直後に認証メールを送信し、 `/register/verify`（メール認証誘導画面）へ遷移
4. 「認証はこちらから」ボタン押下で`/email/verify`（メール認証画面）へ遷移
5. 認証メール内のリンクをクリックすると、
  新しいタブで `/email/verify/{id}/{hash}` にアクセスし認証完了後、勤怠登録画面へ遷移します。

- 認証が未完了のままログインした場合も認証誘導画面へ遷移します。
- 認証メールの再送機能があります。（1分間に6回まで）
- Mailtrapの無料プランには送信レート制限があるため、送信エラーが発生する場合があります。
- その場合は MAIL_MAILER=log を使用して storage/logs/laravel.log の認証URLで動作確認できます。
- `MAIL_MAILER` を変更した場合は `php artisan config:clear` を実行してください。

## テストコード

- **PHPUnit** を用いた Feature テストを実装しています。
- テスト実行時は、**Docker 上の MySQL テスト用データベース（test_db）** を使用します。
- test_db は **MySQL コンテナ起動時に自動作成**されます。
- テスト用の DB 接続設定は **phpunit.xml** に定義しており、APP_KEY は **.env.testing** を参照してテストを実行します。

### テスト実行前の準備

```bash
# DB を含めて初期化（初回 or 作り直し時）
docker-compose down -v
docker-compose up -d --build

# 開発用DBのマイグレーション
docker-compose exec php php artisan migrate --seed
```

### テスト実行方法
以下のどちらかのコマンドで
すべてのFeatureテストを実行できます。

※ 環境によっては phpunit コマンドの使用を推奨します。

#### Laravel の Artisan コマンドを利用
```bash
docker-compose exec php php artisan test
```
#### PHPUnit コマンドを利用
```bash
docker-compose exec php ./vendor/bin/phpunit
```
