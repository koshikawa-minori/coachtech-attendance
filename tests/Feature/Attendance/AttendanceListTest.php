<?php

declare(strict_types=1);

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

final class AttendanceListTest extends TestCase
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

    // 自分が行った勤怠情報が全て表示されている
    public function test_user_can_view_own_attendances_in_month(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $nextDay = $today->copy()->addDay();

        $this->user->update(['name' => '田中 太郎']);
        $secondUser = User::factory()->create(['name' => '山田 花子']);

        $this->createAttendanceForDate($this->user, $today, [
            'clock_in_at' => $today->copy()->setTime(9,0),
            'clock_out_at' => $today->copy()->setTime(18,0),
        ]);

        $this->createAttendanceForDate($this->user, $nextDay, [
            'clock_in_at' => $nextDay->copy()->setTime(9,30),
            'clock_out_at' => $nextDay->copy()->setTime(18,30),
        ]);

        $this->createAttendanceForDate($secondUser, $nextDay, [
            'clock_in_at' => $nextDay->copy()->setTime(10,0),
            'clock_out_at' => $nextDay->copy()->setTime(19,0),
        ]);

        $response = $this->get(route('attendance.index'));

        $response->assertStatus(200);
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
        $response->assertSeeText('09:30');
        $response->assertSeeText('18:30');
        $response->assertDontSeeText('10:00');
        $response->assertDontSeeText('19:00');
    }

    // 勤怠一覧画面に遷移した際に現在の月が表示される
    public function test_attendance_list_shows_current_month_on_first_view(): void
    {
        Carbon::setTestNow(Carbon::create(2026,1,4,9,0,0));

        $response = $this->get(route('attendance.index'));

        $response->assertStatus(200);
        $response->assertSeeText('2026/01');
    }

    // 「前月」を押下した時に表示月の前月の情報が表示される
    public function test_attendance_list_can_move_to_previous_month(): void
    {
        Carbon::setTestNow(Carbon::create(2026,1,4,9,0,0));

        $response = $this->get(route('attendance.index', ['month' => '2025-12']));

        $response->assertStatus(200);
        $response->assertSeeText('2025/12');
    }

    // 「翌月」を押下した時に表示月の前月の情報が表示される
    public function test_attendance_list_can_move_to_next_month(): void
    {
        Carbon::setTestNow(Carbon::create(2026,1,4,9,0,0));

        $response = $this->get(route('attendance.index', ['month' => '2026-02']));

        $response->assertStatus(200);
        $response->assertSeeText('2026/02');
    }

    // 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function test_detail_link_navigates_to_attendance_detail(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $this->user->update(['name' => '田中 太郎']);

        $attendance = $this->createAttendanceForDate($this->user, $today, [
            'clock_in_at' => $today->copy()->setTime(9, 31),
            'clock_out_at' => $today->copy()->setTime(18, 31),
        ]);

        $attendanceIndexResponse = $this->get(route('attendance.index'));
        $attendanceIndexResponse->assertStatus(200);

        $attendanceDetailUrl = route('attendance.detail', ['attendance' => $attendance->id]);
        $attendanceIndexResponse->assertSee($attendanceDetailUrl);

        $attendanceDetailResponse = $this->get($attendanceDetailUrl);
        $attendanceDetailResponse->assertStatus(200);

        $attendanceDetailResponse->assertSeeText('田中 太郎');
    }
}
