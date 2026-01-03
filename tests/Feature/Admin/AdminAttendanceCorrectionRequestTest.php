<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminAttendanceCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_pending_correction_requests(): void
    {
        // TODO: 未承認の修正申請を複数作成 → 承認待ちタブGET → 全件表示
        $this->assertTrue(true);
    }

    public function test_admin_can_view_all_approved_correction_requests(): void
    {
        // TODO: 承認済み申請を作成 → 承認済みタブGET → 全件表示
        $this->assertTrue(true);
    }

    public function test_admin_can_view_correction_request_detail(): void
    {
        // TODO: 申請詳細GET → 内容一致
        $this->assertTrue(true);
    }

    public function test_admin_can_approve_correction_request_and_attendance_is_updated(): void
    {
        // TODO: 承認POST → 勤怠更新 & 申請ステータス更新
        $this->assertTrue(true);
    }
}
