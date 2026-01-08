<?php

declare(strict_types=1);

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    // 勤怠詳細画面の「名前」がログインユーザーの氏名になっている
    public function test_detail_displays_user_name(): void
    {
        // TODO: 詳細GET → 名前がログインユーザー
        $this->assertTrue(true);
    }

    // 勤怠詳細画面の「日付」が選択した日付になっている
    public function test_detail_displays_work_date(): void
    {
        // TODO: 詳細GET → 日付が選択した日付
        $this->assertTrue(true);
    }

    // 「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
    public function test_detail_displays_clock_times(): void
    {
        // TODO: 詳細GET → 出勤・退勤が打刻と一致
        $this->assertTrue(true);
    }

    // 「休憩」にて記されている時間がログインユーザーの打刻と一致している
    public function test_detail_displays_break_times(): void
    {
        // TODO: 詳細GET → 休憩時間が打刻と一致
        $this->assertTrue(true);
    }

}
