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

    public function index(Request $request){
        $userId = Auth::id();
        $headerType = 'user';

        // 表示したい月判定
        $requestedMonth = $request->query('month');

        if ($requestedMonth) {
            $targetMonth  = Carbon::parse($requestedMonth);
        } else {
            $targetMonth  = Carbon::now();
        }

        // 月初と月末をCarbonで作る
        $startOfMonth = $targetMonth ->copy()->startOfMonth();
        $endOfMonth = $targetMonth ->copy()->endOfMonth();

        // 今月の勤怠取得
        $attendances = Attendance::where('user_id', $userId)->whereBetween('work_date', [$startOfMonth, $endOfMonth])->get();

        // 前月と翌月リンクの処理
        $previousMonth = $targetMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetMonth->copy()->addMonth()->format('Y-m');

        // その月の月初から月末までのリスト作り
        $startDateTime = $startOfMonth->copy();
        $endDateTime = $endOfMonth->copy();

        $dates = [];
        $diffDays = $startDateTime->diffInDays($endDateTime);
        for ($dayIndex = 0; $dayIndex <= $diffDays; $dayIndex++)
        {
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

    public function detail(Attendance $attendance)
    {
        // ・表示項目：氏名、日付、出勤・退勤、休憩（複数行）、備考。
        // ・表示内容は打刻内容と一致していること。
        // ・休憩はレコードの数に応じて行を表示し、さらに新規入力フィールドを 1 行表示する。
        // ・承認待ちの場合は修正不可とし、「承認待ちのため修正はできません。」を表示する。
        // ・承認待ち以外の場合は「出勤・退勤」「休憩」「備考」を編集できる。
        $attendance->load('breakTimes');
        $headerType = 'user';

        return view('attendance.attendance_detail', [
            'attendance' => $attendance,
            'headerType' => $headerType,
        ]);
    }

    public function detailRequest(AttendanceTimeRequest $request, Attendance $attendance)
    {
        // ・ユーザーが入力した「出勤・退勤」「休憩」「備考」の修正内容を受け付ける。
        // ・AttendanceTimeRequest（FormRequest）にてバリデーションを実施する。
        // ・バリデーション NG の場合は、元の画面にリダイレクトしエラーメッセージを表示する。
        // ・バリデーション OK の場合は修正申請テーブルに申請内容を登録し、ステータスを「承認待ち」として保存する。
        // ・申請後は、一般ユーザー側の申請一覧「承認待ち」に表示され、管理者の修正申請承認画面にも表示される。
        // ・すでに承認待ち状態の勤怠に対しては追加の修正は行えず、
        //   「承認待ちのため修正はできません。」を表示する。

        $headerType = 'user';

        $validated = $request->validated();

        $clockIn = $validated['clock_in_at'];
        $clockOut = $validated['clock_out_at'];
        $breaksJson = json_encode($validated['breaks']);
        $note = $validated['note'];

        AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'requested_clock_in_at' => $clockIn,
            'requested_clock_out_at' => $clockOut,
            'requested_breaks' => $breaksJson,
            'requested_notes' => $note,
            'status' => 0,
        ]);

        return redirect()->route('attendance.detail', $attendance->id);
    }
}

