<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

final class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['is_admin' => true]);
        $this->actingAs($this->adminUser);
    }

    private function createAttendanceForDay(
        User $user,
        Carbon $targetDay,
        array $overrideAttendance = []
    ): Attendance
    {
        $attendanceAttributes = array_merge([
            'user_id' => $user->id,
            'work_date' => $targetDay->toDateString(),
            'clock_in_at' => $targetDay->copy()->setTime(9,0),
            'clock_out_at' => $targetDay->copy()->setTime(18,0),
            'notes' => null,
        ],
        $overrideAttendance
        );

        return Attendance::create($attendanceAttributes);
    }

    // その日になされた全ユーザーの勤怠情報が正確に確認できる
    public function test_admin_can_view_all_users_attendance_for_today(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $firstUser = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);
        $secondUser = User::factory()->create(['is_admin' => false, 'name' => '山田 花子']);

        $this->createAttendanceForDay($firstUser, $today, [
            'clock_in_at' => $today->copy()->setTime(9,0),
            'clock_out_at' => $today->copy()->setTime(18,0),
        ]);
        $this->createAttendanceForDay($secondUser, $today, [
            'clock_in_at' => $today->copy()->setTime(10,0),
            'clock_out_at' => $today->copy()->setTime(19,0),
        ]);

        $response = $this->get(route('admin.attendance.index'));

        $response->assertStatus(200);

        $response->assertSee('田中 太郎');
        $response->assertSee('山田 花子');

        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    // 遷移した際に現在の日付が表示される
    public function test_admin_attendance_list_shows_today_date_on_first_view(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $response = $this->get(route('admin.attendance.index'));

        $response->assertStatus(200);
        $response->assertSee('2026/01/04');
    }

    // 「前日」を押下した時に前の日の勤怠情報が表示される
    public function test_admin_can_view_previous_day_attendance_list(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $response = $this->get(route('admin.attendance.index', ['date' => '2026-01-03']));

        $response->assertStatus(200);
        $response->assertSee('2026/01/03');
    }

    // 「翌日」を押下した時に次の日の勤怠情報が表示される
    public function test_admin_can_view_next_day_attendance_list(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $response = $this->get(route('admin.attendance.index', ['date' => '2026-01-05']));

        $response->assertStatus(200);
        $response->assertSee('2026/01/05');
    }
}
