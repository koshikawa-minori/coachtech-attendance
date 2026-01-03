<?php

declare(strict_types=1);

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_screen_shows_logged_in_user_name(): void
    {
        // TODO: 詳細GET → 名前がログインユーザー
        $this->assertTrue(true);
    }

    public function test_detail_screen_shows_selected_date_and_times(): void
    {
        // TODO: 詳細GET → 日付/出退勤/休憩が一致
        $this->assertTrue(true);
    }

    public function test_update_fails_when_clock_in_is_after_clock_out(): void
    {
        // TODO: 出勤>退勤で保存 → エラー
        $this->assertTrue(true);
    }

    public function test_update_fails_when_break_start_is_after_clock_out(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_update_fails_when_break_end_is_after_clock_out(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_update_fails_when_note_is_missing(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_update_creates_correction_request_and_it_is_visible_in_request_list(): void
    {
        // TODO: 修正保存 → 申請作成 → 一般/管理者側に表示
        $this->assertTrue(true);
    }
}
