<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function show(Request $request)
    {
        // 現在日時の取得
        $today = today()->format('Y年n月j日');
        $currentTime = now()->format('H:i');
        $weekday = Carbon::now()->isoFormat('ddd');

        $userId = Auth::id();
        $todayAttendance = Attendance::where('user_id', $userId)->whereDate('work_date', today())->first();

        $headerType = 'user';

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
        $actionType = $request->input('action_type');
        $userId = Auth::id();
        $today = today();
        $currentTime = now();

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

    public function index(Request $request){
        $userId = Auth::id();
        $requestedMonth = $request->query('month');
        $headerType = 'user';

        if ($requestedMonth) {
            $targetMonth  = Carbon::parse($requestedMonth);
        } else {
            $targetMonth  = Carbon::now();
        }

        $startOfMonth = $targetMonth ->copy()->startOfMonth();
        $endOfMonth = $targetMonth ->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $userId)->whereBetween('work_date', [$startOfMonth, $endOfMonth])->get();

        $previousMonth = $targetMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetMonth->copy()->addMonth()->format('Y-m');

        $startDateTime = $startOfMonth->copy();
        $endDateTime = $endOfMonth->copy();

        $dates = [];
        $diffDays = $startDateTime->diffInDays($endDateTime);
        for ($dayIndex = 0; $dayIndex <= $diffDays; $dayIndex++) {
            $dates[] = $startDateTime->format('Y-m-d');
            $startDateTime->addDays();
        }

        return view('attendance.attendance_list', [
            'attendances' => $attendances,
            'startOfMonth' => $startOfMonth,
            'previousMonth' => $previousMonth,
            'nextMonth' => $nextMonth,
            'dates' => $dates,
            'headerType' => $headerType,
        ]);
    }
}
