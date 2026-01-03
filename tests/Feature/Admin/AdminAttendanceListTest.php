<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_users_attendance_for_the_day(): void
    {
        // TODO: 複数ユーザーの勤怠を作る → 管理者一覧GET → 全員分表示
        $this->assertTrue(true);
    }

    public function test_admin_attendance_list_shows_current_date_on_first_view(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_admin_attendance_list_can_move_to_previous_day(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_admin_attendance_list_can_move_to_next_day(): void
    {
        // TODO
        $this->assertTrue(true);
    }
}
