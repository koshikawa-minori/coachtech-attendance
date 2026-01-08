<?php

declare(strict_types=1);

namespace Tests\Feature\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AttendanceCorrectionListTest extends TestCase
{
    use RefreshDatabase;

    // 「承認待ち」にログインユーザーが行った申請が全て表示されていること
    public function test_list_shows_all_pending_requests_for_user(): void
    {
        // TODO: 一覧GET → 承認待ちに自分の申請が全件表示
        $this->assertTrue(true);
    }

    // 「承認済み」に管理者が承認した修正申請が全て表示されている
    public function test_list_shows_all_approved_requests_for_user(): void
    {
        // TODO: 一覧GET → 承認済みに承認済み申請が全件表示
        $this->assertTrue(true);
    }

    // 各申請の「詳細」を押下すると勤怠詳細画面に遷移する
    public function test_request_detail_link_redirects_to_attendance_detail(): void
    {
        // TODO: 詳細リンクアクセス → 勤怠詳細へ遷移
        $this->assertTrue(true);
    }
}
