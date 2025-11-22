<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceController extends Controller
{
    public function show(Request $request)
    {
        // 現在日時の取得
        $today = today()->format('Y年n月j日');
        $currentTime = now()->format('H:i');
        $weekday = now()->isoFormat('ddd');

        $userId = Auth::id();
        $todayAttendance = Attendance::where('user_id', $userId)->whereDate('work_date', today())->first();

        if (is_null($todayAttendance)) {
            $status = 'before_work';
        }elseif (is_null($todayAttendance->clock_out_at)) {
            $status = 'working';
        }else{
            $status = 'after_work';
        }

        return view('attendance.attendance', compact('today', 'currentTime', 'status', 'weekday'));

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

                if($todayAttendance) {
                        // 新規作成はせず、そのままリダイレクト
                } else {
                        // ログインユーザーID＋今日の日付＋現在時刻で勤怠レコードを作成しリダイレクト
                        $todayAttendance = Attendance::create([
                            'user_id' => $userId,
                            'work_date' => $today,
                            'clock_in_at' => $currentTime,
                            ]);
                }
                return redirect()->route('attendance.show');

                break;

            case 'break_start':
                // 休憩開始
                break;

            case 'break_end':
                // 休憩戻り
                break;

            case 'clock_out':
                // 退勤
                break;
        }

        return redirect()->route('attendance.show');

    }
}
