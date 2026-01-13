<?php

declare(strict_types=1);

namespace Tests\Feature\Admin\Request;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrection;
use Carbon\Carbon;

final class AdminAttendanceCorrectionTest extends TestCase
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

    // 承認待ちの修正申請が全て表示されている
    public function test_admin_list_shows_all_pending_requests(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $firstUser = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);
        $secondUser = User::factory()->create(['is_admin' => false, 'name' => '山田 花子']);

        $firstAttendance = Attendance::create([
            'user_id' => $firstUser->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(9,0),
            'clock_out_at' => $today->copy()->setTime(18,0),
            'notes' => null,
        ]);

        $seccondAttendance = Attendance::create([
            'user_id' => $secondUser->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(10,0),
            'clock_out_at' => $today->copy()->setTime(19,0),
            'notes' => null,
        ]);

        $firstAttendanceRequest = AttendanceCorrection::create([
            'attendance_id' => $firstAttendance->id,
            'requested_clock_in_at' => '09:30',
            'requested_clock_out_at' => '18:30',
            'requested_notes' => '申請承認待ち1',
            'status' => false,
        ]);

        $approvedAttendanceForFirstUser = AttendanceCorrection::create([
            'attendance_id' => $firstAttendance->id,
            'requested_clock_in_at' => '09:30',
            'requested_clock_out_at' => '18:30',
            'requested_notes' => '申請承認済み1',
            'status' => true,
        ]);

        $seccondAttendanceRequest = AttendanceCorrection::create([
            'attendance_id' => $seccondAttendance->id,
            'requested_clock_in_at' => '10:30',
            'requested_clock_out_at' => '19:30',
            'requested_notes' => '申請承認待ち2',
            'status' => false,
        ]);

        $response = $this->get('/admin/requests?page=wait');

        $response->assertStatus(200);
        $response->assertSeeText('2026/01/04');
        $response->assertSeeText('田中 太郎');
        $response->assertSeeText('山田 花子');
        $response->assertSeeText('申請承認待ち1');
        $response->assertSeeText('申請承認待ち2');
        $response->assertDontSeeText('申請承認済み1');
    }

    // 承認済みの修正申請が全て表示されている
    public function test_admin_list_shows_all_approved_requests(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $firstUser = User::factory()->create(['is_admin' => false, 'name' => '田中 太郎']);
        $secondUser = User::factory()->create(['is_admin' => false, 'name' => '山田 花子']);

        $firstAttendance = Attendance::create([
            'user_id' => $firstUser->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(9,0),
            'clock_out_at' => $today->copy()->setTime(18,0),
            'notes' => null,
        ]);

        $seccondAttendance = Attendance::create([
            'user_id' => $secondUser->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(10,0),
            'clock_out_at' => $today->copy()->setTime(19,0),
            'notes' => null,
        ]);

        $firstAttendanceRequest = AttendanceCorrection::create([
            'attendance_id' => $firstAttendance->id,
            'requested_clock_in_at' => '09:30',
            'requested_clock_out_at' => '18:30',
            'requested_notes' => '申請承認済み1',
            'status' => true,
        ]);

        $pendingRequestForFirstUser = AttendanceCorrection::create([
            'attendance_id' => $firstAttendance->id,
            'requested_clock_in_at' => '09:30',
            'requested_clock_out_at' => '18:30',
            'requested_notes' => '申請承認待ち1',
            'status' => false,
        ]);

        $seccondAttendanceRequest = AttendanceCorrection::create([
            'attendance_id' => $seccondAttendance->id,
            'requested_clock_in_at' => '10:30',
            'requested_clock_out_at' => '19:30',
            'requested_notes' => '申請承認済み2',
            'status' => true,
        ]);

        $response = $this->get('/admin/requests?page=done');

        $response->assertStatus(200);
        $response->assertSeeText('2026/01/04');
        $response->assertSeeText('田中 太郎');
        $response->assertSeeText('山田 花子');
        $response->assertSeeText('申請承認済み1');
        $response->assertSeeText('申請承認済み2');
        $response->assertDontSeeText('申請承認待ち1');
    }

    // 修正申請の詳細内容が正しく表示されている
    public function test_admin_show_displays_request_details(): void
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

        $requestedClockIn = $today->copy()->setTime(9,30);
        $requestedClockOut = $today->copy()->setTime(18,30);

        $attendanceCorrection = AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => $requestedClockIn,
            'requested_clock_out_at' => $requestedClockOut,
            'requested_notes' => 'ログインユーザー承認待ち',
            'status' => false,
        ]);

        $response = $this->get(route('admin.requests.index'));
        $response->assertStatus(200);

        $requestDetailUrl = route('admin.requests.show', ['attendanceCorrection' => $attendanceCorrection->id]);
        $response->assertSee($requestDetailUrl);

        $response = $this->get($requestDetailUrl);
        $response->assertStatus(200);
        $response->assertSeeText('田中 太郎');
        $response->assertSeeText('09:30');
        $response->assertSeeText('18:30');
        $response->assertSeeText('ログインユーザー承認待ち');
    }

    // 修正申請の承認処理が正しく行われる
    public function test_admin_approve_updates_attendance_and_request_status(): void
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

        $requestedClockIn = $today->copy()->setTime(9,30);
        $requestedClockOut = $today->copy()->setTime(18,30);
        $requestedBreakTime = [
            ['start' => '12:30', 'end' => '13:30'],
        ];

        $attendanceCorrection = AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => $requestedClockIn,
            'requested_clock_out_at' => $requestedClockOut,
            'requested_breaks' => $requestedBreakTime,
            'requested_notes' => 'ログインユーザー承認待ち',
            'status' => false,
        ]);

        $response = $this->from(route('admin.requests.show', ['attendanceCorrection' => $attendanceCorrection->id]))->post(route('admin.requests.approve', ['attendanceCorrection' => $attendanceCorrection->id]));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.requests.show', ['attendanceCorrection' => $attendanceCorrection->id]));

        $this->assertDatabaseCount(table: 'attendance_requests', count: 1);
        $this->assertDatabaseHas('attendance_requests', [
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => $requestedClockIn->toDateTimeString(),
            'requested_clock_out_at' => $requestedClockOut->toDateTimeString(),
            'requested_notes' => 'ログインユーザー承認待ち',
            'status' => true,
            'reviewed_admin_id' => $this->adminUser->id,
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in_at' => $requestedClockIn->toDateTimeString(),
            'clock_out_at' => $requestedClockOut->toDateTimeString(),
            'notes' => 'ログインユーザー承認待ち',
        ]);

        $expectedBreakStart = Carbon::createFromFormat('Y-m-d H:i', $attendance->work_date->format('Y-m-d') . ' 12:30');
        $expectedBreakEnd = Carbon::createFromFormat('Y-m-d H:i', $attendance->work_date->format('Y-m-d') . ' 13:30');

        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
            'break_start_at' => $expectedBreakStart->toDateTimeString(),
            'break_end_at' => $expectedBreakEnd->toDateTimeString(),
        ]);

        $breakStart = Carbon::createFromFormat('Y-m-d H:i', $attendance->work_date->format('Y-m-d') . ' 12:00');
        $breakEnd = Carbon::createFromFormat('Y-m-d H:i', $attendance->work_date->format('Y-m-d') . ' 13:00');

        $this->assertDatabaseMissing('break_times', [
            'attendance_id' => $attendance->id,
            'break_start_at' => $breakStart->toDateTimeString(),
            'break_end_at' => $breakEnd->toDateTimeString(),
        ]);
    }
}
