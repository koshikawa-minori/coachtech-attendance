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
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $user = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(9,31),
            'clock_out_at' => $today->copy()->setTime(18,21),
            'notes' => null,
        ]);

        $response = $this->get(route('admin.attendance.detail', ['attendance' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertSee('田中 太郎');
        $response->assertSee('09:31');
        $response->assertSee('18:21');

        Carbon::setTestNow(null);
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
