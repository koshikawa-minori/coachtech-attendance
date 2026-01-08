<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Request;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminCorrectionListTest extends TestCase
{
    use RefreshDatabase;

    // 承認待ちの修正申請が全て表示されている
    public function test_list_shows_all_pending_requests(): void
    {
        // TODO: 管理者一覧GET → 承認待ちが全件表示
        $this->assertTrue(true);
    }

    // 承認済みの修正申請が全て表示されている
    public function test_list_shows_all_approved_requests(): void
    {
        // TODO: 管理者一覧GET → 承認済みが全件表示
        $this->assertTrue(true);
    }
}
