<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
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

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    private function makeUpdatePayload(array $overridePayload = []): array
    {
        $defaultPayload = [
            'clock_in_at' => '09:00',
            'clock_out_at' => '18:00',
            'breaks' => [
                ['start' => '12:00', 'end' => '13:00'],
            ],
            'note' => '電車遅延のため',
        ];

        return array_replace_recursive($defaultPayload, $overridePayload);
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
        $response->assertSeeText('田中 太郎');
        $response->assertSee('09:31');
        $response->assertSee('18:21');
    }

    // 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_update_fails_when_clock_in_after_clock_out(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $user = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(9,0),
            'clock_out_at' => $today->copy()->setTime(18,0),
            'notes' => null,
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => $today->copy()->setTime(12,0),
            'break_end_at' => $today->copy()->setTime(13,0),
        ]);

        $payload = $this->makeUpdatePayload([
            'clock_in_at' => '19:00',
            'clock_out_at' => '18:00',
        ]);

        $response = $this->from(route('admin.attendance.detail', ['attendance' => $attendance->id]))->post(route('admin.attendance.update', ['attendance' => $attendance->id]), $payload);

        $response->assertRedirect(route('admin.attendance.detail', ['attendance' => $attendance->id]));
        $response->assertSessionHasErrors([
            'clock_in_at' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    // 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_update_fails_when_break_start_after_clock_out(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $user = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(9,0),
            'clock_out_at' => $today->copy()->setTime(18,0),
            'notes' => null,
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => $today->copy()->setTime(12,0),
            'break_end_at' => $today->copy()->setTime(13,0),
        ]);

        $payload = $this->makeUpdatePayload([
            'breaks' => [
                ['start' => '19:00', 'end' => '13:00'],
            ],
        ]);

        $response = $this->from(route('admin.attendance.detail', ['attendance' => $attendance->id]))->post(route('admin.attendance.update', ['attendance' => $attendance->id]), $payload);

        $response->assertRedirect(route('admin.attendance.detail', ['attendance' => $attendance->id]));
        $response->assertSessionHasErrors([
            'breaks.0.start' => '休憩開始時間が不適切な値です',
        ]);
    }

    // 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_update_fails_when_break_end_after_clock_out(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $user = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(9,0),
            'clock_out_at' => $today->copy()->setTime(18,0),
            'notes' => null,
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => $today->copy()->setTime(12,0),
            'break_end_at' => $today->copy()->setTime(13,0),
        ]);

        $payload = $this->makeUpdatePayload([
            'breaks' => [
                ['start' => '12:00', 'end' => '19:00'],
            ],
        ]);

        $response = $this->from(route('admin.attendance.detail', ['attendance' => $attendance->id]))->post(route('admin.attendance.update', ['attendance' => $attendance->id]), $payload);

        $response->assertRedirect(route('admin.attendance.detail', ['attendance' => $attendance->id]));
        $response->assertSessionHasErrors([
            'breaks.0.end' => '休憩終了時間もしくは退勤時間が不適切な値です',
        ]);
    }

    // 備考欄が未入力の場合のエラーメッセージが表示される
    public function test_admin_update_fails_when_note_is_required(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $user = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(9,0),
            'clock_out_at' => $today->copy()->setTime(18,0),
            'notes' => null,
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => $today->copy()->setTime(12,0),
            'break_end_at' => $today->copy()->setTime(13,0),
        ]);

        $payload = $this->makeUpdatePayload([
            'note' => '',
        ]);

        $response = $this->from(route('admin.attendance.detail', ['attendance' => $attendance->id]))->post(route('admin.attendance.update', ['attendance' => $attendance->id]), $payload);

        $response->assertRedirect(route('admin.attendance.detail', ['attendance' => $attendance->id]));
        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }
}
