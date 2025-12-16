<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
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
            $targetDay = Carbon::createFromFormat('Y-m-d', $requestedDay);
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

    public function show(Attendance $attendance)
    {
        $attendance->load([
            'user',
            'breakTimes',
            'attendanceCorrection',
        ]);

        return view('admin.attendance.admin_attendance_detail', [
        'headerType' => 'admin',
        'attendance' => $attendance,
        ]);
    }

    public function update(AttendanceTimeRequest $request, Attendance $attendance)
    {
        if ($attendance->attendanceCorrection && $attendance->attendanceCorrection->status == false)
        {
            return back()->with('error', '*承認待ちのため修正はできません。');
        }

        $validated = $request->validated();

        $clockIn = $validated['clock_in_at'];
        $clockOut = $validated['clock_out_at'];

        $workDate = $attendance->work_date;
        $workDateTime = Carbon::parse($workDate)->format('Y-m-d');

        $clockInDateTime = $workDateTime . ' ' . $clockIn;
        $clockOutDateTime = $workDateTime . ' ' . $clockOut;
        $clockInCarbon = Carbon::createFromFormat('Y-m-d H:i', $clockInDateTime);
        $clockOutCarbon = Carbon::createFromFormat('Y-m-d H:i', $clockOutDateTime);

        $notes = $validated['note'];

        $attendance->update([
            'clock_in_at' => $clockInCarbon,
            'clock_out_at' => $clockOutCarbon,
            'notes' => $notes,
        ]);

        $attendance->breakTimes()->delete();
        foreach ($validated['breaks'] as $breakDate)
            {
                $breakStart = $breakDate['start'];
                $breakEnd = $breakDate['end'];

                if (empty($breakStart) || empty($breakEnd)) {
                continue;
                }

                $breakStartDateTime = $workDateTime . ' ' . $breakStart;
                $breakEndDateTime = $workDateTime . ' ' . $breakEnd;
                $breakStartDateTimeCarbon = Carbon::createFromFormat('Y-m-d H:i', $breakStartDateTime);
                $breakEndDateTimeCarbon = Carbon::createFromFormat('Y-m-d H:i', $breakEndDateTime);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start_at' => $breakStartDateTimeCarbon,
                    'break_end_at' => $breakEndDateTimeCarbon,
                ]);
            }

        return redirect()->route('admin.attendance.detail', $attendance->id);
    }
}
