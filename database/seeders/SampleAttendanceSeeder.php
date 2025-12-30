<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrection;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SampleAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 管理者ユーザー
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => '管理者',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // スタッフリスト
        $staffList = [
            ['name' => '山田 太郎', 'email' => 'taro.y@coachtech.com'],
            ['name' => '増田 一世', 'email' => 'issei.m@coachtech.com'],
            ['name' => '山本 敬吉', 'email' => 'keikichi.y@coachtech.com'],
            ['name' => '秋田 朋美', 'email' => 'tomomi.a@coachtech.com'],
            ['name' => '中西 敬夫', 'email' => 'norio.n@coachtech.com'],
        ];

        $staffs = [];
        foreach ($staffList as $staff) {
            $staffUser = User::firstOrCreate(
                ['email' => $staff['email']],
                [
                    'name' => $staff['name'],
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                    'email_verified_at' => now(),
                ]
            );
            $staffs[] = $staffUser;
        }

        // 各スタッフに勤怠作成
        $staffCurrentMonthStart = Carbon::now()->startOfMonth();
        $attendanceIdsByStaff = [];
        foreach ($staffs as $staff) {
            $weekdayCounter = 0;
            $workDatePointer = $staffCurrentMonthStart->copy();

            while ($weekdayCounter < 5) {
                if ($workDatePointer->isWeekend()) {
                    $workDatePointer->addDay();
                    continue;
                }

                $clockIn = $workDatePointer->copy()->setTime(9, 0);
                $clockOut = $workDatePointer->copy()->setTime(18, 0);
                $breakStart = $workDatePointer->copy()->setTime(12, 0);
                $breakEnd = $workDatePointer->copy()->setTime(13, 0);

                $staffAttendance = Attendance::updateOrCreate(
                    ['user_id' => $staff->id, 'work_date' => $workDatePointer->toDateString()],
                    ['clock_in_at' => $clockIn, 'clock_out_at' => $clockOut]
                );

                BreakTime::where('attendance_id', $staffAttendance->id)->delete();

                BreakTime::create([
                    'attendance_id' => $staffAttendance->id,
                    'break_start_at' => $breakStart,
                    'break_end_at' => $breakEnd,
                ]);

                if ($weekdayCounter === 0) {
                    $attendanceIdsByStaff[$staff->id]['day1'] = $staffAttendance->id;
                }
                if ($weekdayCounter === 1) {
                    $attendanceIdsByStaff[$staff->id]['day2']  = $staffAttendance->id;
                }

                $weekdayCounter++;
                $workDatePointer->addDay();
            }
        }

        // 各スタッフに承認済み1件ずつ作成
        foreach ($staffs as $staff) {
            $attendanceId = $attendanceIdsByStaff[$staff->id]['day1'];
            $attendanceModel = Attendance::findOrFail($attendanceId);
            $workDateCarbon = Carbon::parse($attendanceModel->work_date);
            $requestedClockInCarbon = $workDateCarbon->copy()->setTime(10, 0);
            $requestedClockOutCarbon = $workDateCarbon->copy()->setTime(19, 0);

            AttendanceCorrection::updateOrCreate(
                ['attendance_id' => $attendanceId],
                [
                    'requested_clock_in_at' => $requestedClockInCarbon,
                    'requested_clock_out_at' => $requestedClockOutCarbon,
                    'requested_breaks' => [
                        ['start' => '12:00', 'end' => '13:00'],
                        ['start' => null, 'end' => null],
                    ],
                    'requested_notes' => '電車遅延のため',
                    'status' => true,
                    'reviewed_admin_id' => $admin->id,
                    'reviewed_at' => now(),
                ]
            );
        }

        // 各スタッフに承認待ち1件ずつ作成
        foreach ($staffs as $staff) {
            $pendingAttendanceId = $attendanceIdsByStaff[$staff->id]['day2'];
            $pendingAttendance = Attendance::findOrFail($pendingAttendanceId);
            AttendanceCorrection::updateOrCreate(
                ['attendance_id' => $pendingAttendanceId],
                [
                    'requested_clock_in_at' => $pendingAttendance->clock_in_at,
                    'requested_clock_out_at' => $pendingAttendance->clock_out_at,
                    'requested_breaks' => [
                        ['start' => '12:00', 'end' => '13:00'],
                        ['start' => null, 'end' => null],
                    ],
                    'requested_notes' => '電車遅延のため',
                    'status' => false,
                    'reviewed_admin_id' => null,
                    'reviewed_at' => null,
                ]
            );
        }

        // 一般ユーザー
        $mainUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => '西 伶奈',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => null,
            ],
        );

        // 勤怠(前月・今月・翌月)データ作成
        $months = [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->startOfMonth(),
            Carbon::now()->addMonth()->startOfMonth(),
        ];

        $mainUserAttendanceIds = [];
        $currentMonthStart = Carbon::now()->startOfMonth();

        $firstWeekdayOfCurrentMonth = $currentMonthStart->copy();
        while ($firstWeekdayOfCurrentMonth->isWeekend()) {
            $firstWeekdayOfCurrentMonth->addDay();
        }

        foreach ($months as $targetMonth) {
            $startOfMonth = $targetMonth->copy()->startOfMonth();
            $endOfMonth = $targetMonth->copy()->endOfMonth();
            $datePeriod = CarbonPeriod::create($startOfMonth, $endOfMonth);

            foreach ($datePeriod as $workDate) {
                if ($workDate->isWeekend()) {
                    continue;
                }

                $clockIn = $workDate->copy()->setTime(9, 0);
                $clockOut = $workDate->copy()->setTime(18, 0);
                $breakStart = $workDate->copy()->setTime(12, 0);
                $breakEnd = $workDate->copy()->setTime(13, 0);

                $attendance = Attendance::updateOrCreate(
                    ['user_id' => $mainUser->id, 'work_date' => $workDate->toDateString()],
                    ['clock_in_at' => $clockIn, 'clock_out_at' => $clockOut]
                );

                // seedを繰り返しても休憩が重複しない処理
                BreakTime::where('attendance_id', $attendance->id)->delete();

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start_at' => $breakStart,
                    'break_end_at' => $breakEnd,
                ]);

                if ($targetMonth->isSameMonth($currentMonthStart) && $workDate->isSameDay($firstWeekdayOfCurrentMonth)) {
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start_at' => $workDate->copy()->setTime(15, 0),
                        'break_end_at' => $workDate->copy()->setTime(15, 15),
                    ]);
                }
                $mainUserAttendanceIds[] = $attendance->id;
            }
        }

        // 承認済み作成
        $approvedAttendanceIds = collect($mainUserAttendanceIds)->shuffle()->take(2)->values()->all();
        foreach ($approvedAttendanceIds as $mainUserAttendanceId) {
            $mainUserAttendanceModel = Attendance::findOrFail($mainUserAttendanceId);
            $workDateCarbon = Carbon::parse($mainUserAttendanceModel->work_date);
            $requestedClockInCarbon = $workDateCarbon->copy()->setTime(10, 0);
            $requestedClockOutCarbon = $workDateCarbon->copy()->setTime(19, 0);

            AttendanceCorrection::updateOrCreate(
                ['attendance_id' => $mainUserAttendanceId],
                [
                    'requested_clock_in_at' => $requestedClockInCarbon,
                    'requested_clock_out_at' => $requestedClockOutCarbon,
                    'requested_breaks' => [
                        ['start' => '12:00', 'end' => '13:00'],
                        ['start' => null, 'end' => null],
                    ],
                    'requested_notes' => '電車遅延のため',
                    'status' => true,
                    'reviewed_admin_id' => $admin->id,
                    'reviewed_at' => now(),
                ]
            );
        }

        // 承認待ち作成
        $pendingAttendanceModels = Attendance::whereIn('id', $mainUserAttendanceIds)->whereNotIn('id', $approvedAttendanceIds)->inRandomOrder()->limit(10)->get();

        foreach ($pendingAttendanceModels as $pendingAttendanceModel) {
            AttendanceCorrection::updateOrCreate(
                ['attendance_id' => $pendingAttendanceModel->id],
                [
                    'requested_clock_in_at' => $pendingAttendanceModel->clock_in_at,
                    'requested_clock_out_at' => $pendingAttendanceModel->clock_out_at,
                    'requested_breaks' => [
                        ['start' => '12:00', 'end' => '13:00'],
                        ['start' => null, 'end' => null],
                    ],
                    'requested_notes' => '電車遅延のため',
                    'status' => false,
                    'reviewed_admin_id' => null,
                    'reviewed_at' => null,
                ]
            );
        }
    }
}
