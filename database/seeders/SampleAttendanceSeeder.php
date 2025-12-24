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

        // 一般ユーザー
        $mainUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => '西 伶奈',
                'password' => Hash::make('password'),
                'is_admin' => false,

            // 提出前に null に戻す！！
            // 'email_verified_at' => null,
                'email_verified_at' => now(), // ← 開発中は認証済みでOK
            ],
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

        $month = Carbon::now()->startOfMonth();
        $firstAttendanceIds = [];
        foreach ($staffs as $staff) {
            $weekdayCounter = 0;
            $datePointer = $month->copy();

            while ($weekdayCounter < 5) {
                if ($datePointer->isWeekend()) {
                    $datePointer->addDay();
                    continue;
                }

                $clockInAt = $datePointer->copy()->setTime(9, 0);
                $clockOutAt = $datePointer->copy()->setTime(18, 0);
                $breakStartAt = $datePointer->copy()->setTime(12, 0);
                $breakEndAt = $datePointer->copy()->setTime(13, 0);

                $staffAttendance = Attendance::create([
                    'user_id' => $staff->id,
                    'work_date' => $datePointer->toDateString(),
                    'clock_in_at' => $clockInAt,
                    'clock_out_at' => $clockOutAt,
                ]);

                BreakTime::create([
                    'attendance_id' => $staffAttendance->id,
                    'break_start_at' => $breakStartAt,
                    'break_end_at' => $breakEndAt,
                ]);

                if ($weekdayCounter === 0) {
                    $firstAttendanceIds[$staff->id] = $staffAttendance->id;
                }
                $weekdayCounter++;
                $datePointer->addDay();
            }
        }

        $targetStaffs = array_slice($staffs, 0,2);
        foreach ($targetStaffs as $targetStaff) {
            $attendance = $firstAttendanceIds[$targetStaff->id];
            AttendanceCorrection::create([
                    'attendance_id' => $attendance,
                    'requested_clock_in_at' => Carbon::parse('2025-12-01 10:00'),
                    'requested_clock_out_at' => Carbon::parse('2025-12-01 19:00'),
                    'requested_breaks' => [
                        ['start' => '12:00', 'end' => '13:00'],
                        ['start' => null, 'end' => null],
                    ],
                    'requested_notes' => '電車遅延のため',
                    'status' => true,
                    'reviewed_admin_id' => $admin->id,
                    'reviewed_at' => now(),
                ]);

        }

        // 勤怠(前月・今月・翌月)データ作成
        $months = [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->startOfMonth(),
            Carbon::now()->addMonth()->startOfMonth(),
        ];

        $createdAttendances = [];

        foreach ($months as $targetMonth) {
            $startOfMonth = $targetMonth->copy()->startOfMonth();
            $endOfMonth = $targetMonth->copy()->endOfMonth();

            $datePeriod = CarbonPeriod::create($startOfMonth, $endOfMonth);

            foreach ($datePeriod as $workDate) {

                if ($workDate->isWeekend()) {
                    continue;
                }

                $clockInAt = $workDate->copy()->setTime(9, 0);
                $clockOutAt = $workDate->copy()->setTime(18, 0);
                $breakStartAt = $workDate->copy()->setTime(12, 0);
                $breakEndAt = $workDate->copy()->setTime(13, 0);

                $attendance = Attendance::create([
                    'user_id' => $mainUser->id,
                    'work_date' => $workDate->toDateString(),
                    'clock_in_at' => $clockInAt,
                    'clock_out_at' => $clockOutAt,
                ]);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start_at' => $breakStartAt,
                    'break_end_at' => $breakEndAt,
                ]);

                $createdAttendances[] = $attendance->id;
            }
        }

        $attendances = Attendance::whereIn('id', $createdAttendances)->inRandomOrder()->limit(10)->get();

        foreach ($attendances as $attendance) {
            AttendanceCorrection::create([
                    'attendance_id' => $attendance->id,
                    'requested_clock_in_at' => $attendance->clock_in_at,
                    'requested_clock_out_at' => $attendance->clock_out_at,
                    'requested_breaks' => [
                        ['start' => '12:00', 'end' => '13:00'],
                        ['start' => null, 'end' => null],
                    ],
                    'requested_notes' => '電車遅延のため',
                    'status' => false,
                    'reviewed_admin_id' => null,
                    'reviewed_at' => null,
                ]);

        }
    }
}
