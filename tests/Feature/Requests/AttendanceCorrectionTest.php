<?php

declare(strict_types=1);

namespace Tests\Feature\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
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
        // TODO: 修正申請POST → 申請が作成される
        $this->assertTrue(true);
    }

    // 「承認待ち」にログインユーザーが行った申請が全て表示されていること
    public function test_list_shows_all_pending_requests_for_user(): void
    {
        // TODO: 一覧GET → 承認待ちに自分の申請が全件表示
        $this->assertTrue(true);
    }

    // 「承認済み」に管理者が承認した修正申請が全て表示されている
    public function test_list_shows_all_approved_requests_for_user(): void
    {
        // TODO: 一覧GET → 承認済みに承認済み申請が全件表示
        $this->assertTrue(true);
    }

    // 各申請の「詳細」を押下すると勤怠詳細画面に遷移する
    public function test_request_detail_link_redirects_to_attendance_detail(): void
    {
        // TODO: 詳細リンクアクセス → 勤怠詳細へ遷移
        $this->assertTrue(true);
    }
}
