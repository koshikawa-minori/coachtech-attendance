<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StaffListTest extends TestCase
{
    use RefreshDatabase;

    // ID14
    // 管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
    public function test_admin_can_view_all_staff_names_and_emails(): void
    {
        // TODO: 一般ユーザー作成 → 管理者でスタッフ一覧GET → 氏名/メール表示
        $this->assertTrue(true);
    }

    // ユーザーの勤怠情報が正しく表示される
    public function test_admin_can_view_selected_staff_attendance_list(): void
    {
        // TODO: スタッフ別勤怠一覧GET → 表示確認
        $this->assertTrue(true);
    }

    // 「前月」を押下した時に表示月の前月の情報が表示される
    public function test_admin_staff_attendance_list_can_move_to_previous_month(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    // 「翌月」を押下した時に表示月の前月の情報が表示される
    public function test_admin_staff_attendance_list_can_move_to_next_month(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    // 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function test_admin_staff_attendance_list_detail_link_navigates_to_detail_screen(): void
    {
        // TODO
        $this->assertTrue(true);
    }
}
