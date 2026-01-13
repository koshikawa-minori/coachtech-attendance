<?php

declare(strict_types=1);

namespace Tests\Feature\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrection;
use Carbon\Carbon;

final class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    private function createUserAttendanceForDate(
        Carbon $date,
        array $overrideAttendance = []
    ): Attendance
    {
        $attendanceAttributes = array_merge([
            'user_id' => $this->user->id,
            'work_date' => $date->toDateString(),
            'clock_in_at' => $date->copy()->setTime(9,0),
            'clock_out_at' => $date->copy()->setTime(18,0),
            'notes' => null,
        ],
        $overrideAttendance
        );

        return Attendance::create($attendanceAttributes);
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

    // 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_request_rejects_clock_in_after_clock_out(): void
    {
        $today = Carbon::create(2026, 1, 4, 9, 0, 0);
        Carbon::setTestNow($today);

        $attendance = $this->createUserAttendanceForDate($today);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => $today->copy()->setTime(12,0),
            'break_end_at' => $today->copy()->setTime(13,0),
        ]);

        $payload = $this->makeUpdatePayload([
            'clock_in_at' => '19:00',
            'clock_out_at' => '18:00',
        ]);

        $response = $this->from(route('attendance.detail', ['attendance' => $attendance->id]))->post(route('attendance.detail.request', ['attendance' => $attendance->id]), $payload);

        $response->assertRedirect(route('attendance.detail', ['attendance' => $attendance->id]));
        $response->assertSessionHasErrors([
            'clock_in_at' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    // 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_request_rejects_break_start_after_clock_out(): void
    {
        $today = Carbon::create(2026, 1, 4, 9, 0, 0);
        Carbon::setTestNow($today);

        $attendance = $this->createUserAttendanceForDate($today);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => $today->copy()->setTime(12,0),
            'break_end_at' => $today->copy()->setTime(13,0),
        ]);

        $payload = $this->makeUpdatePayload([
            'breaks' => [
                ['start' => '19:00', 'end' => '19:30'],
            ],
        ]);

        $response = $this->from(route('attendance.detail', ['attendance' => $attendance->id]))->post(route('attendance.detail.request', ['attendance' => $attendance->id]), $payload);

        $response->assertRedirect(route('attendance.detail', ['attendance' => $attendance->id]));
        $response->assertSessionHasErrors([
            'breaks.0.start' => '休憩開始時間が不適切な値です',
        ]);
    }

    // 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_request_rejects_break_end_after_clock_out(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $attendance = $this->createUserAttendanceForDate($today);

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

        $response = $this->from(route('attendance.detail', ['attendance' => $attendance->id]))->post(route('attendance.detail.request', ['attendance' => $attendance->id]), $payload);

        $response->assertRedirect(route('attendance.detail', ['attendance' => $attendance->id]));
        $response->assertSessionHasErrors([
            'breaks.0.end' => '休憩終了時間もしくは退勤時間が不適切な値です',
        ]);
    }

    // 備考欄が未入力の場合のエラーメッセージが表示される
    public function test_request_rejects_missing_note(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $attendance = $this->createUserAttendanceForDate($today);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => $today->copy()->setTime(12,0),
            'break_end_at' => $today->copy()->setTime(13,0),
        ]);

        $payload = $this->makeUpdatePayload([
            'note' => '',
        ]);

        $response = $this->from(route('attendance.detail', ['attendance' => $attendance->id]))->post(route('attendance.detail.request', ['attendance' => $attendance->id]), $payload);

        $response->assertRedirect(route('attendance.detail', ['attendance' => $attendance->id]));
        $response->assertSessionHasErrors([
            'note' => '備考を記入してください',
        ]);
    }

    // 修正申請処理が実行される
    public function test_request_creates_correction(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $attendance = $this->createUserAttendanceForDate($today);

        $payload = $this->makeUpdatePayload([
            'clock_in_at' => '09:10',
            'clock_out_at' => '18:10',
        ]);

        $response = $this->from(route('attendance.detail', ['attendance' => $attendance->id]))->post(route('attendance.detail.request', ['attendance' => $attendance->id]), $payload);

        $requestedClockIn = $today->copy()->setTime(9,10);
        $requestedClockOut = $today->copy()->setTime(18,10);
        $this->assertDatabaseCount(table: 'attendance_requests', count: 1);
        $this->assertDatabaseHas('attendance_requests', [
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => $requestedClockIn->toDateTimeString(),
            'requested_clock_out_at' => $requestedClockOut->toDateTimeString(),
            'status' => false,
        ]);

        $attendanceCorrection = AttendanceCorrection::where('attendance_id', $attendance->id)->firstOrFail();

        $this->assertSame([
            ['start' => '12:00', 'end' => '13:00'],
        ], $attendanceCorrection->requested_breaks);
    }

    // 「承認待ち」にログインユーザーが行った申請が全て表示されていること
    public function test_list_shows_all_pending_requests_for_user(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $attendance = $this->createUserAttendanceForDate($today);

        $firstAttendance = AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => '09:30',
            'requested_clock_out_at' => '18:30',
            'requested_notes' => 'ログインユーザー承認待ち1',
            'status' => false,
        ]);

        $seccondAttendance = AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => '10:30',
            'requested_clock_out_at' => '19:30',
            'requested_notes' => 'ログインユーザー承認待ち2',
            'status' => false,
        ]);

        $otherUser = User::factory()->create([]);

        $othersAttendance = Attendance::create([
            'user_id' => $otherUser->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(9,31),
            'clock_out_at' => $today->copy()->setTime(18,21),
            'notes' => null,
        ]);

        AttendanceCorrection::create([
            'attendance_id' => $othersAttendance->id,
            'requested_clock_in_at' => '11:30',
            'requested_clock_out_at' => '20:30',
            'requested_notes' => '他ユーザー承認待ち1',
            'status' => false,
        ]);

        $response = $this->get('/requests?page=wait');

        $response->assertStatus(200);
        $response->assertSeeText('ログインユーザー承認待ち1');
        $response->assertSeeText('ログインユーザー承認待ち2');
        $response->assertDontSeeText('他ユーザー承認待ち1');
    }

    // 「承認済み」に管理者が承認した修正申請が全て表示されている
    public function test_list_shows_all_approved_requests_for_user(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $attendance = $this->createUserAttendanceForDate($today);

        $firstAttendance = AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => '09:30',
            'requested_clock_out_at' => '18:30',
            'requested_notes' => 'ログインユーザー承認済み1',
            'status' => true,
        ]);

        $seccondAttendance = AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => '10:30',
            'requested_clock_out_at' => '19:30',
            'requested_notes' => 'ログインユーザー承認済み2',
            'status' => true,
        ]);

        $otherUser = User::factory()->create([]);

        $othersAttendance = Attendance::create([
            'user_id' => $otherUser->id,
            'work_date' => $today->toDateString(),
            'clock_in_at' => $today->copy()->setTime(9,31),
            'clock_out_at' => $today->copy()->setTime(18,21),
            'notes' => null,
        ]);

        AttendanceCorrection::create([
            'attendance_id' => $othersAttendance->id,
            'requested_clock_in_at' => '11:30',
            'requested_clock_out_at' => '20:30',
            'requested_notes' => '他ユーザー承認済み1',
            'status' => true,
        ]);

        $response = $this->get('/requests?page=done');

        $response->assertStatus(200);
        $response->assertSeeText('ログインユーザー承認済み1');
        $response->assertSeeText('ログインユーザー承認済み2');
        $response->assertDontSeeText('他ユーザー承認済み1');
    }

    // 各申請の「詳細」を押下すると勤怠詳細画面に遷移する
    public function test_request_detail_link_redirects_to_attendance_detail(): void
    {
        $today = Carbon::create(2026,1,4,9,0,0);
        Carbon::setTestNow($today);

        $this->user->update(['name' => '田中 太郎']);

        $attendance = $this->createUserAttendanceForDate($today);

        $requestedClockIn = $today->copy()->setTime(9,30);
        $requestedClockOut = $today->copy()->setTime(18,30);

        AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => $requestedClockIn,
            'requested_clock_out_at' => $requestedClockOut,
            'requested_notes' => 'ログインユーザー承認待ち',
            'status' => false,
        ]);

        $response = $this->get(route('requests.index'));
        $response->assertStatus(200);

        $attendanceDetailUrl = route('attendance.detail', ['attendance' => $attendance->id]);
        $response->assertSee($attendanceDetailUrl);

        $response = $this->get($attendanceDetailUrl);
        $response->assertStatus(200);
        $response->assertSeeText('田中 太郎');
        $response->assertSee('value="09:30"', false);
        $response->assertSee('value="18:30"', false);
    }
}
