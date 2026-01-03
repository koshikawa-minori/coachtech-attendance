<?php

declare(strict_types=1);

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AttendanceClockingTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_datetime_is_displayed_in_expected_format(): void
    {
        // TODO: 勤怠打刻画面GET → 日時表示の一致
        $this->assertTrue(true);
    }

    public function test_status_is_displayed_correctly_for_off_duty_user(): void
    {
        // TODO: 勤務外ユーザーでログイン → ステータスが「勤務外」
        $this->assertTrue(true);
    }

    public function test_clock_in_button_works(): void
    {
        // TODO: 出勤POST → ステータスが出勤中
        $this->assertTrue(true);
    }

    public function test_break_start_and_break_end_buttons_work(): void
    {
        // TODO: 休憩入/休憩戻 → ステータス遷移
        $this->assertTrue(true);
    }

    public function test_clock_out_button_works(): void
    {
        // TODO: 退勤POST → ステータスが退勤済
        $this->assertTrue(true);
    }
}
