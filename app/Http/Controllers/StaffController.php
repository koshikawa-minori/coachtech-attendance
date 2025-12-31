<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::where('is_admin', 0)->get();

        return view('admin.staff.staff_list', [
            'headerType' => 'admin',
            'users' => $users,
        ]);
    }

    public function attendance(Request $request, $staffId)
    {
        $staffUser = User::findOrFail($staffId);
        $targetMonth = $this->getTargetMonth($request);

        // 月初と月末をCarbonで作る
        $startOfMonth = $targetMonth->copy()->startOfMonth();
        $endOfMonth = $targetMonth->copy()->endOfMonth();

        // 今月の勤怠取得 work_dateをキーにする
        $attendancesByDate = Attendance::where('user_id', $staffId)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(function (Attendance $attendance) {
                return $attendance->work_date->format('Y-m-d');
            });

        // 表示中の月を基準に、前月・翌月の切り替え用クエリを作成
        $previousMonth = $targetMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetMonth->copy()->addMonth()->format('Y-m');

        // 表示データ作成
        $rows = [];
        $datePointer = $startOfMonth->copy();

        while ($datePointer->lte($endOfMonth)) {
            $dateKey = $datePointer->format('Y-m-d');
            $attendanceForDate = $attendancesByDate->get($dateKey);

            $rows[] = [
                'formatted_date' => $datePointer->isoFormat('MM/DD(ddd)'),
                'clock_in' => $attendanceForDate?->clock_in_at?->format('H:i'),
                'clock_out' => $attendanceForDate?->clock_out_at?->format('H:i'),
                'break_total' => $attendanceForDate?->the_total_break,
                'work_total' => $attendanceForDate?->the_total_work,
                'attendance_id' => $attendanceForDate?->id,
            ];

            $datePointer->addDay();
        }

        return view('admin.staff.staff_attendance', [
            'headerType' => 'admin',
            'rows' => $rows,
            'startOfMonth' => $startOfMonth,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
            'staffUser' => $staffUser,
        ]);
    }

    public function export(Request $request, $staffId)
    {
        $filename = 'attendance_' . now()->format('Ymd_His') . '.csv';

        $targetMonth = $this->getTargetMonth($request);
        $startOfMonth = $targetMonth->copy()->startOfMonth();
        $endOfMonth = $targetMonth->copy()->endOfMonth();

        return response()->streamDownload(function () use ($staffId, $startOfMonth, $endOfMonth) {

            $stream = fopen('php://output', 'w');

            fputcsv($stream, ['日付', '出勤', '退勤', '休憩', '合計']);

            $attendancesByDate = Attendance::where('user_id', $staffId)
                ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
                ->get()
                ->keyBy(function (Attendance $attendance) {
                    return $attendance->work_date->format('Y-m-d');
                });

            $datePointer = $startOfMonth->copy();
            while ($datePointer->lte($endOfMonth)) {
                $dateKey = $datePointer->format('Y-m-d');
                $attendanceForDate = $attendancesByDate->get($dateKey);

                $csvRow = [
                    $datePointer->isoFormat('MM/DD(ddd)'),
                    $attendanceForDate?->clock_in_at?->format('H:i') ?? '',
                    $attendanceForDate?->clock_out_at?->format('H:i') ?? '',
                    $attendanceForDate?->the_total_break ?? '',
                    $attendanceForDate?->the_total_work ?? '',
                ];

                fputcsv($stream, $csvRow);

                $datePointer->addDay();
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function getTargetMonth(Request $request): Carbon
    {
        // 表示したい月判定
        $requestedMonth = $request->query('month');

        if ($requestedMonth) {
            return Carbon::createFromFormat('Y-m-d', $requestedMonth . '-01')->startOfMonth();
        }

        return Carbon::now()->startOfMonth();
    }
}
