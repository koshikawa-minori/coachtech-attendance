<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function show(Request $request)
    {
        // 現在日時の取得
        $today = today()->format('Y年n月j日');
        $currentTime = now()->format('H:i');
        $weekday = now()->isoFormat('ddd');

        $userId = Auth::id();
        $isExists = Attendance::where('user_id', $userId)->whereDate('work_date', today())->first();

        if($isExists) {
                $status = 'working';
        } elseif {
                $status = 'after_work';
        } else {
                $status = 'before_work';
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
                $isExists = Attendance::where('user_id', $userId)->whereDate('work_date', today())->exists();

                if($isExists) {
                        // 新規作成はせず、そのままリダイレクト
                } else {
                        // ログインユーザーID＋今日の日付＋現在時刻で勤怠レコードを作成してからリダイレクト
                        $attendance = Attendance::create([
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
