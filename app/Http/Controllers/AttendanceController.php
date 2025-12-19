<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\BreakTime;
use Carbon\Carbon;
use App\Http\Requests\AttendanceTimeRequest;

class AttendanceController extends Controller
{
    public function show(Request $request)
    {
        $headerType = 'user';

        // 現在日時の取得
        $today = today()->format('Y年n月j日');
        $currentTime = now()->format('H:i');
        $weekday = Carbon::now()->isoFormat('ddd');

        // ログインユーザーの今日の勤怠取得
        $userId = Auth::id();
        $todayAttendance = Attendance::where('user_id', $userId)->whereDate('work_date', today())->first();

        if (is_null($todayAttendance)) {
            $status = 'before_work';

        } elseif (is_null($todayAttendance->clock_out_at)) {
            $todayBreak = $todayAttendance->breakTimes();
            $breakTime = $todayBreak->whereNull('break_end_at')->orderByDesc('break_start_at')->first();

            if ($breakTime) {
                $status = 'on_break';
            } else {
                $status = 'working';
            }

        } else {
            $status = 'after_work';
            $headerType = 'user_clock_out';
        }

        return view('attendance.attendance', compact('today', 'currentTime', 'status', 'weekday', 'headerType'));

    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $today = today();
        $currentTime = now();

        // どのボタンが押されたか取得
        $actionType = $request->input('action_type');

        switch ($actionType) {
            case 'clock_in':
                $todayAttendance = Attendance::where('user_id', $userId)->whereDate('work_date', today())->exists();

                if(!$todayAttendance) {
                    $todayAttendance = Attendance::create([
                        'user_id' => $userId,
                        'work_date' => $today,
                        'clock_in_at' => $currentTime,
                    ]);
                }
                return redirect()->route('attendance.show');
                break;

            case 'break_start':
                $todayAttendance = Attendance::where('user_id', $userId)->whereDate('work_date', today())->first();

                $todayBreak = $todayAttendance->breakTimes();
                $breakTime = $todayBreak->whereNull('break_end_at')->orderByDesc('break_start_at')->first();

                $attendanceId = $todayAttendance->id;
                $breakStart = now();

                if(!$breakTime) {
                    BreakTime::create([
                        'attendance_id' => $attendanceId,
                        'break_start_at' => $breakStart,
                    ]);
                }

                return redirect()->route('attendance.show');
                break;

            case 'break_end':
                $todayAttendance = Attendance::where('user_id', $userId)->whereDate('work_date', today())->first();

                $todayBreak = $todayAttendance->breakTimes();
                $breakTime = $todayBreak->whereNull('break_end_at')->orderByDesc('break_start_at')->first();

                if($breakTime) {
                    $breakTime->update([
                        'break_end_at' => now(),
                    ]);
                }

                return redirect()->route('attendance.show');
                break;

            case 'clock_out':
                $todayAttendance = Attendance::where('user_id', $userId)->whereDate('work_date', today())->first();

                if($todayAttendance) {
                    $todayAttendance->update([
                        'clock_out_at' => now(),
                    ]);
                }

                return redirect()->route('attendance.show');
                break;
        }

        return redirect()->route('attendance.show');

    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $headerType = 'user';

        // 表示したい月判定
        $requestedMonth = $request->query('month');

        if ($requestedMonth) {
            $targetMonth  = Carbon::createFromFormat('Y-m', $requestedMonth)->startOfMonth();
        } else {
            $targetMonth  = Carbon::now()->startOfMonth();
        }

        // 月初と月末をCarbonで作る
        $startOfMonth = $targetMonth ->copy()->startOfMonth();
        $endOfMonth = $targetMonth ->copy()->endOfMonth();

        // 今月の勤怠取得 work_dateをキーにする
        $attendancesByDate = Attendance::where('user_id', $userId)
        ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
        ->get()
        ->keyBy(function (Attendance $attendance) {
            return $attendance->work_date->format('Y-m-d');
        });

        // 前月と翌月リンクの処理
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

        return view('attendance.attendance_list', [
            'headerType' => $headerType,
            'rows' => $rows,
            'startOfMonth' => $startOfMonth,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
        ]);
    }

    public function detail(Attendance $attendance)
    {
        $attendance->load('breakTimes');
        $headerType = 'user';

        return view('attendance.attendance_detail', [
            'attendance' => $attendance,
            'headerType' => $headerType,
        ]);
    }

    public function detailRequest(AttendanceTimeRequest $request, Attendance $attendance)
    {
        if ($attendance->attendanceCorrection && $attendance->attendanceCorrection->status == false)
            {
            return back()->with('error', '*承認待ちのため修正はできません。');
        }

        $headerType = 'user';

        $validated = $request->validated();

        $clockIn = $validated['clock_in_at'];
        $clockOut = $validated['clock_out_at'];

        $workDate = $attendance->work_date;
        $workDateTime = Carbon::parse($workDate)->format('Y-m-d');

        // Carbonで扱うために日付と時刻を結合して日時文字列を作成
        $clockInDateTime = $workDateTime . ' ' . $clockIn;
        $clockOutDateTime = $workDateTime . ' ' . $clockOut;
        $clockInCarbon = Carbon::createFromFormat('Y-m-d H:i', $clockInDateTime);
        $clockOutCarbon = Carbon::createFromFormat('Y-m-d H:i', $clockOutDateTime);

        $breaksJson = json_encode($validated['breaks']);
        $note = $validated['note'];

        AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => $clockInCarbon,
            'requested_clock_out_at' => $clockOutCarbon,
            'requested_breaks' => $breaksJson,
            'requested_notes' => $note,
            'status' => 0,
        ]);

        return redirect()->route('attendance.detail', $attendance->id);
    }
}

