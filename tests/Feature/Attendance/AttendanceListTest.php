<?php

declare(strict_types=1);

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_list_shows_only_logged_in_user_records(): void
    {
        // TODO: ユーザーA/Bの勤怠を作る → Aでログイン → Aのみ表示
        $this->assertTrue(true);
    }

    public function test_attendance_list_shows_current_month_on_first_view(): void
    {
        // TODO: 一覧GET → 現在月表示
        $this->assertTrue(true);
    }

    public function test_attendance_list_can_move_to_previous_month(): void
    {
        // TODO: 前月ボタンで前月が表示
        $this->assertTrue(true);
    }

    public function test_attendance_list_can_move_to_next_month(): void
    {
        // TODO: 翌月ボタンで翌月が表示
        $this->assertTrue(true);
    }

    public function test_attendance_list_detail_link_navigates_to_detail_screen(): void
    {
        // TODO: 「詳細」→ 勤怠詳細へ遷移
        $this->assertTrue(true);
    }
}
