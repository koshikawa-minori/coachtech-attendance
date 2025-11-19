<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function show(Request $request)
    {
        // 現在日時の取得
        $today = today()->format('Y年n月j日');
        $currentTime = now()->format('H:i');
        $weekday = now()->isoFormat('ddd');

        $status = 'before_work';

        return view('attendance.attendance', compact('today', 'currentTime', 'status', 'weekday'));

    }

    public function store(Request $request)
    {
        $actionType = $request->input('action_type');

        switch ($actionType) {
            case 'clock_in':
                // 出勤
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
