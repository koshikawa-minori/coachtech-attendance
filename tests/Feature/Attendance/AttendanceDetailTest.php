<?php

declare(strict_types=1);

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

final class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 1, 4, 9, 0, 0));

        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => Carbon::now(),
        ]);

        Carbon::setTestNow(null);

        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    private function createAttendanceForDate(
        User $user,
        Carbon $date,
        array $overrideAttendance = []
    ): Attendance
    {
        $attendanceAttributes = array_merge([
            'user_id' => $user->id,
            'work_date' => $date->toDateString(),
            'clock_in_at' => $date->copy()->setTime(9,0),
            'clock_out_at' => $date->copy()->setTime(18,0),
            'notes' => null,
        ],
        $overrideAttendance
        );

        return Attendance::create($attendanceAttributes);
    }

    // 勤怠詳細画面の「名前」がログインユーザーの氏名になっている
    public function test_detail_displays_user_name(): void
    {
        $today = Carbon::create(2026,1,4);

        $attendance = $this->createAttendanceForDate($this->user, $today);

        $response = $this->get(route('attendance.detail', ['attendance' => $attendance->id]));
        $response->assertStatus(200);
        $response->assertSeeText($this->user->name);
    }

    // 勤怠詳細画面の「日付」が選択した日付になっている
    public function test_detail_displays_work_date(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);

        $attendance = $this->createAttendanceForDate($this->user, $today);

        $response = $this->get(route('attendance.detail', ['attendance' => $attendance->id]));
        $response->assertStatus(200);

        $response->assertSeeText('2026年');
        $response->assertSeeText('01月04日');
    }

    // 「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
    public function test_detail_displays_clock_times(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);

        $attendance = $this->createAttendanceForDate($this->user, $today, [
            'clock_in_at' => $today->copy()->setTime(9, 0),
            'clock_out_at' => $today->copy()->setTime(18, 0),
        ]);

        $response = $this->get(route('attendance.detail', ['attendance' => $attendance->id]));
        $response->assertStatus(200);

        $response->assertSee('id="clock_in_at"', false);
        $response->assertSee('value="09:00"', false);

        $response->assertSee('id="clock_out_at"', false);
        $response->assertSee('value="18:00"', false);
    }

    // 「休憩」にて記されている時間がログインユーザーの打刻と一致している
    public function test_detail_displays_break_times(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);

        $attendance = $this->createAttendanceForDate($this->user, $today, [
            'clock_in_at' => $today->copy()->setTime(9, 0),
            'clock_out_at' => $today->copy()->setTime(18, 0),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => $today->copy()->setTime(12, 0),
            'break_end_at' => $today->copy()->setTime(13, 0),
        ]);

        $response = $this->get(route('attendance.detail', ['attendance' => $attendance->id]));
        $response->assertStatus(200);

        $response->assertSee('id="break_start_0"', false);
        $response->assertSee('value="12:00"', false);

        $response->assertSee('id="break_end_0"', false);
        $response->assertSee('value="13:00"', false);
    }

}
