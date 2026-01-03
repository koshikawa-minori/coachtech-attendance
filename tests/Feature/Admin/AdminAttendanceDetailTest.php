<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_detail_screen_shows_selected_attendance_data(): void
    {
        // TODO: 対象勤怠を作る → 詳細GET → 表示一致
        $this->assertTrue(true);
    }

    public function test_admin_update_fails_when_clock_in_is_after_clock_out(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_admin_update_fails_when_break_start_is_after_clock_out(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_admin_update_fails_when_break_end_is_after_clock_out(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_admin_update_fails_when_note_is_missing(): void
    {
        // TODO
        $this->assertTrue(true);
    }
}
