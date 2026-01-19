<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Staff;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

final class StaffListTest extends TestCase
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

    private function createAttendanceForMonth(
        User $user,
        Carbon $targetMonth,
        array $overrideAttendance = []
    ): Attendance
    {
        $attendanceAttributes = array_merge([
            'user_id' => $user->id,
            'work_date' => $targetMonth->toDateString(),
            'clock_in_at' => $targetMonth->copy()->setTime(9,0),
            'clock_out_at' => $targetMonth->copy()->setTime(18,0),
            'notes' => null,
        ],
        $overrideAttendance
        );

        return Attendance::create($attendanceAttributes);
    }
    // 管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
    public function test_admin_can_view_staff_list(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $firstUser = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎', 'email' => 'test1@example.com']);
        $secondUser = User::factory()->create(['is_admin' => false, 'name' => '山田 花子', 'email' => 'test2@example.com']);

        $response = $this->get(route('admin.staff.index'));

        $response->assertStatus(200);

        $response->assertSeeText('田中 太郎');
        $response->assertSeeText('山田 花子');

        $response->assertSeeText('test1@example.com');
        $response->assertSeeText('test2@example.com');
    }

    // ユーザーの勤怠情報が正しく表示される
    public function test_admin_can_view_staff_attendance_list(): void
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

        $response = $this->get(route('admin.staff.attendance', ['staffId' => $user->id,]));

        $response->assertStatus(200);
        $response->assertSeeText('09:00');
        $response->assertSeeText('18:00');
    }

    // 「前月」を押下した時に表示月の前月の情報が表示される
    public function test_admin_can_move_staff_attendance_to_previous_month(): void
    {
        Carbon::setTestNow(Carbon::create(2026,1,4,9,0,0));

        $user = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);

        $response = $this->get(route('admin.staff.attendance', ['staffId' => $user->id, 'month' => '2025-12']));

        $response->assertStatus(200);
        $response->assertSeeText('2025/12');
    }

    // 「翌月」を押下した時に表示月の前月の情報が表示される
    public function test_admin_can_move_staff_attendance_to_next_month(): void
    {
        Carbon::setTestNow(Carbon::create(2026,1,4,9,0,0));

        $user = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);

        $response = $this->get(route('admin.staff.attendance', ['staffId' => $user->id, 'month' => '2026-02']));

        $response->assertStatus(200);
        $response->assertSeeText('2026/02');
    }

    // 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function test_admin_can_navigate_to_staff_attendance_detail(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $user = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);

        $attendance = $this->createAttendanceForMonth($user, $today, [
            'clock_in_at' => $today->copy()->setTime(9, 31),
            'clock_out_at' => $today->copy()->setTime(18, 31),
        ]);

        $attendanceResponse = $this->get(route('admin.staff.attendance', ['staffId' => $user->id]));
        $attendanceResponse->assertStatus(200);

        $attendanceDetailUrl = route('admin.attendance.detail', ['attendance' => $attendance->id]);
        $attendanceResponse->assertSee($attendanceDetailUrl);

        $attendanceDetailResponse = $this->get($attendanceDetailUrl);
        $attendanceDetailResponse->assertStatus(200);

        $attendanceDetailResponse->assertSeeText('田中 太郎');
    }
}
