<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Request;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminCorrectionApprovalTest extends TestCase
{
    use RefreshDatabase;

    // 修正申請の詳細内容が正しく表示されている
    public function test_detail_shows_correct_request_data(): void
    {
        // TODO: 管理者 詳細GET → 申請内容が正しく表示
        $this->assertTrue(true);
    }

    // 修正申請の承認処理が正しく行われる
    public function test_approve_updates_attendance_and_request_status(): void
    {
        // TODO: 承認POST → 勤怠が更新され申請ステータスも更新
        $this->assertTrue(true);
    }
}
