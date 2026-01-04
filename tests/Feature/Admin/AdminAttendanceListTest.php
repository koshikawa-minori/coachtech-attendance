<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    // その日になされた全ユーザーの勤怠情報が正確に確認できる
    public function test_admin_can_view_all_users_attendance_for_today(): void
    {
        // TODO: 複数ユーザーの勤怠を作る → 管理者一覧GET → 全員分表示
        $this->assertTrue(true);
    }

    // 遷移した際に現在の日付が表示される
    public function test_admin_attendance_list_shows_today_date_on_first_view(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    // 「前日」を押下した時に前の日の勤怠情報が表示される
    public function test_admin_can_view_previous_day_attendance_list(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    // 「翌日」を押下した時に次の日の勤怠情報が表示される
    public function test_admin_can_view_next_day_attendance_list(): void
    {
        // TODO
        $this->assertTrue(true);
    }
}
