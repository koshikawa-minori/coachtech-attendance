<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

final class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['is_admin' => true]);
        $this->actingAs($this->adminUser);
    }

    // 勤怠詳細画面に表示されるデータが選択したものになっている
    public function test_admin_attendance_detail_displays_correct_data(): void
    {
        // TODO: 対象勤怠を作る → 詳細GET → 表示一致
        $this->assertTrue(true);
    }

    // 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_update_fails_when_clock_in_after_clock_out(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    // 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_update_fails_when_break_start_after_clock_out(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    // 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_update_fails_when_break_end_after_clock_out(): void
    {
        // TODO
        $this->assertTrue(true);
    }

    // 備考欄が未入力の場合のエラーメッセージが表示される
    public function test_admin_update_fails_when_note_is_required(): void
    {
        // TODO
        $this->assertTrue(true);
    }
}
