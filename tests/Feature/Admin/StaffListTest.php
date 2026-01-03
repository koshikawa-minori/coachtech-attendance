<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StaffListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_staff_names_and_emails(): void
    {
        // TODO: 一般ユーザー作成 → 管理者でスタッフ一覧GET → 氏名/メール表示
        $this->assertTrue(true);
    }

    public function test_admin_can_view_selected_staff_attendance_list(): void
    {
        // TODO: スタッフ別勤怠一覧GET → 表示確認
        $this->assertTrue(true);
    }

    public function test_admin_staff_attendance_list_can_move_to_previous_month(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_admin_staff_attendance_list_can_move_to_next_month(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    public function test_admin_staff_attendance_list_detail_link_navigates_to_detail_screen(): void
    {
        // TODO
        $this->assertTrue(true);
    }
}
