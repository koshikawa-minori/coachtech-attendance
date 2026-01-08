<?php

declare(strict_types=1);

namespace Tests\Feature\Attendance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

final class AttendanceClockingTest extends TestCase
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

    // 現在の日時情報がUIと同じ形式で出力されている
    public function test_current_datetime_is_displayed_correctly(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 4, 9, 0, 0));

        $response = $this->get(route('attendance.show'));
        $response->assertSeeText('2026年1月4日');
        $response->assertSeeText('09:00');
        $response->assertSeeText('日');
    }

    // 勤務外の場合、勤怠ステータスが正しく表示される
    public function test_status_is_off_duty(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 4, 9, 0, 0));

        $response = $this->get(route('attendance.show'));

        $response->assertStatus(200);
        $response->assertSeeText('勤務外');
    }

    // 出勤中の場合、勤怠ステータスが正しく表示される
    public function test_status_is_working(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.show'));

        $response->assertStatus(200);
        $response->assertSeeText('出勤中');
    }

    // 休憩中の場合、勤怠ステータスが正しく表示される
    public function test_status_is_on_break(): void
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.show'));

        $response->assertStatus(200);
        $response->assertSeeText('休憩中');
    }

    // 退勤済の場合、勤怠ステータスが正しく表示される
    public function test_status_is_after_work(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
            'clock_out_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.show'));

        $response->assertStatus(200);
        $response->assertSeeText('退勤済');
    }

    // 出勤ボタンが正しく機能する
    public function test_can_clock_in(): void
    {
        $response = $this->get(route('attendance.show'));

        $response->assertStatus(200);
        $response->assertSee('value="clock_in"', false);
        $response->assertSeeText('出勤');

        $response = $this->post(route('attendance.store'), ['action_type' => 'clock_in']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));

        $response->assertStatus(200);
        $response->assertSeeText('出勤中');
    }

    // 出勤は一日一回のみできる
    public function test_cannot_clock_in_twice_in_a_day(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
            'clock_out_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.show'));

        $response->assertStatus(200);
        $response->assertSeeText('退勤済');

        $response->assertDontSee('value="clock_in"', false);

    }

    // 出勤時刻が勤怠一覧画面で確認できる
    public function test_clock_in_time_is_shown_on_attendance_list(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 4, 9, 0, 0));

        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('09:00');
    }

    // 休憩ボタンが正しく機能する
    public function test_can_start_break(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.show'));

        $response->assertStatus(200);
        $response->assertSee('value="break_start"', false);
        $response->assertSeeText('休憩入');

        $response = $this->post(route('attendance.store'), ['action_type' => 'break_start']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('休憩中');
    }

    // 休憩は一日に何回でもできる
    public function test_can_take_break_multiple_times(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.show'));
        $response = $this->post(route('attendance.store'), ['action_type' => 'break_start']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('休憩中');

        $response = $this->get(route('attendance.show'));
        $response = $this->post(route('attendance.store'), ['action_type' => 'break_end']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('出勤中');

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSee('value="break_start"', false);
        $response->assertSeeText('休憩入');
    }

    // 休憩戻ボタンが正しく機能する
    public function test_can_end_break(): void
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('休憩中');

        $response = $this->get(route('attendance.show'));
        $response = $this->post(route('attendance.store'), ['action_type' => 'break_end']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('出勤中');
    }

    // 休憩戻は一日に何回でもできる
    public function test_can_end_break_multiple_times(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.show'));
        $response = $this->post(route('attendance.store'), ['action_type' => 'break_start']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('休憩中');

        $response = $this->get(route('attendance.show'));
        $response = $this->post(route('attendance.store'), ['action_type' => 'break_end']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('出勤中');

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSee('value="break_start"', false);
        $response->assertSeeText('休憩入');

        $response = $this->get(route('attendance.show'));
        $response = $this->post(route('attendance.store'), ['action_type' => 'break_start']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSee('value="break_end"', false);
        $response->assertSeeText('休憩中');
    }

    // 休憩時刻が勤怠一覧画面で確認できる
    public function test_break_time_is_shown_on_attendance_list(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 4, 12, 0, 0));

        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.show'));
        $response = $this->post(route('attendance.store'), ['action_type' => 'break_start']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('休憩中');

        Carbon::setTestNow(Carbon::now()->addHour());

        $response = $this->get(route('attendance.show'));
        $response = $this->post(route('attendance.store'), ['action_type' => 'break_end']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('出勤中');

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('01:00');
    }

    // 退勤ボタンが正しく機能する
    public function test_can_clock_out(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'work_date' => Carbon::today(),
            'clock_in_at' => Carbon::now(),
        ]);

        $response = $this->get(route('attendance.show'));
        $response->assertSee('value="clock_out"', false);
        $response->assertSeeText('退勤');

        $response = $this->post(route('attendance.store'), ['action_type' => 'clock_out']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('退勤済');
    }

    // 退勤時刻が勤怠一覧画面で確認できる
    public function test_clock_out_time_is_shown_on_attendance_list(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 1, 4, 9, 0, 0));

        $response = $this->get(route('attendance.show'));

        $response = $this->post(route('attendance.store'), ['action_type' => 'clock_in']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('出勤中');

        Carbon::setTestNow(Carbon::now()->addHours(8));

        $response = $this->post(route('attendance.store'), ['action_type' => 'clock_out']);
        $response->assertStatus(302);
        $response->assertRedirect(route('attendance.show'));

        $response = $this->get(route('attendance.show'));
        $response->assertStatus(200);
        $response->assertSeeText('退勤済');

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('17:00');
    }

}
