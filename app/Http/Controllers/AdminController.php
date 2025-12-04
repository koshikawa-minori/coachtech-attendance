<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\BreakTime;
use Carbon\Carbon;
use App\Http\Requests\AttendanceTimeRequest;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // 表示したい日判定
        $requestedDay = $request->query('date');

        if ($requestedDay) {
            $targetDay  = Carbon::parse($requestedDay);
        } else {
            $targetDay  = Carbon::today();
        }

        // 前日と翌日リンクの処理
        $previousDay = $targetDay->copy()->subDay()->format('Y-m-d');
        $nextDay = $targetDay->copy()->addDay()->format('Y-m-d');

        // 今日の勤怠取得
        $attendances = Attendance::whereDate('work_date', $targetDay)->with('user')->orderBy('user_id')->get();

        return view('admin.attendance.admin_attendance_list', [
            'headerType' => 'admin',
            'attendances' => $attendances,
            'targetDay' => $targetDay,
            'previousDay' => $previousDay,
            'nextDay' => $nextDay,
        ]);
    }

    public function show(Attendance $attendance){
        return view('admin.attendance.admin_attendance_detail', [
        'headerType' => 'admin',
        'attendance' => $attendance,
    ]);
    }
}
