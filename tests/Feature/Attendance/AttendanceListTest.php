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

        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

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
        // TODO: ユーザーA/Bの勤怠を作る → Aでログイン → Aのみ表示
        $this->assertTrue(true);
    }

    // 勤怠一覧画面に遷移した際に現在の月が表示される
    public function test_attendance_list_shows_current_month_on_first_view(): void
    {
        Carbon::setTestNow(
            Carbon::create(2026,1,4,9,0,0)
        );

        $response = $this->get(route('attendance.show'));
        $response->assertSeeText('2026年1月4日');
        $response->assertSeeText('09:00');
        $response->assertSeeText('日');
    }

    // 「前月」を押下した時に表示月の前月の情報が表示される
    public function test_attendance_list_can_move_to_previous_month(): void
    {
        // TODO: 前月ボタンで前月が表示
        $this->assertTrue(true);
    }

    // 「翌月」を押下した時に表示月の前月の情報が表示される
    public function test_attendance_list_can_move_to_next_month(): void
    {
        // TODO: 翌月ボタンで翌月が表示
        $this->assertTrue(true);
    }

    // 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function test_detail_link_navigates_to_attendance_detail(): void
    {
        // TODO: 「詳細」→ 勤怠詳細へ遷移
        $this->assertTrue(true);
    }
}
