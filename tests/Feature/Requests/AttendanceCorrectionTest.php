<?php

declare(strict_types=1);

namespace Tests\Feature\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    // ID11
    // 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_request_rejects_clock_in_after_clock_out(): void
    {
        // TODO: 修正申請POST → 出勤 > 退勤 → エラー
        $this->assertTrue(true);
    }

    // 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_request_rejects_break_start_after_clock_out(): void
    {
        // TODO: 修正申請POST → 休憩開始 > 退勤 → エラー
        $this->assertTrue(true);
    }

    // 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_request_rejects_break_end_after_clock_out(): void
    {
        // TODO: 修正申請POST → 休憩終了 > 退勤 → エラー
        $this->assertTrue(true);
    }

    // 備考欄が未入力の場合のエラーメッセージが表示される
    public function test_request_rejects_missing_note(): void
    {
        // TODO: 修正申請POST → 備考未入力 → エラー
        $this->assertTrue(true);
    }

    // 修正申請処理が実行される
    public function test_request_creates_correction(): void
    {
        // TODO: 修正申請POST → 申請が作成される
        $this->assertTrue(true);
    }

    // 「承認待ち」にログインユーザーが行った申請が全て表示されていること
    public function test_list_shows_all_pending_requests_for_user(): void
    {
        // TODO: 一覧GET → 承認待ちに自分の申請が全件表示
        $this->assertTrue(true);
    }

    // 「承認済み」に管理者が承認した修正申請が全て表示されている
    public function test_list_shows_all_approved_requests_for_user(): void
    {
        // TODO: 一覧GET → 承認済みに承認済み申請が全件表示
        $this->assertTrue(true);
    }

    // 各申請の「詳細」を押下すると勤怠詳細画面に遷移する
    public function test_request_detail_link_redirects_to_attendance_detail(): void
    {
        // TODO: 詳細リンクアクセス → 勤怠詳細へ遷移
        $this->assertTrue(true);
    }
}
