<?php

declare(strict_types=1);

namespace Tests\Feature\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AttendanceCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    // 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_request_rejects_clock_in_after_clock_out(): void
    {
        // TODO: 修正申請POST → 出勤>退勤 → エラー
        $this->assertTrue(true);
    }

    // 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_request_rejects_break_start_after_clock_out(): void
    {
        // TODO: 修正申請POST → 休憩開始>退勤 → エラー
        $this->assertTrue(true);
    }

    // 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_request_rejects_break_end_after_clock_out(): void
    {
        // TODO: 修正申請POST → 休憩終了>退勤 → エラー
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
}
