<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;
use Carbon\Carbon;

class AdminAttendanceCorrectionController extends Controller
{
    public function index(Request $request)
    {
        $headerType = 'admin';

        // 全一般ユーザーの申請を取得
        $attendanceCorrectionQuery = AttendanceCorrection::with(['attendance.user']);

        $page = $request->query('page', 'wait');

        if ($page === 'done') {
            $attendanceCorrectionQuery->where('status', true);
        } else {
            $attendanceCorrectionQuery->where('status', false);
        }

        // 申請データを降順で取得
        $attendanceCorrections = $attendanceCorrectionQuery->orderByDesc('created_at')->get();

        return view('admin.request.admin_request_list', compact('headerType', 'attendanceCorrections', 'page'));
    }

    public function show(AttendanceCorrection $attendanceCorrection)
    {
        $headerType = 'admin';
        $attendanceCorrection->load(['attendance.user', 'attendance.breakTimes']);

        return view('admin.request.admin_approval', compact('headerType', 'attendanceCorrection'));
    }

    public function approve(AttendanceCorrection $attendanceCorrection)
    {
        if ($attendanceCorrection->status) {
            return back();
        }

        DB::transaction(function () use ($attendanceCorrection) {

            // 同時押し対策
            $updatedRows = AttendanceCorrection::where('id', $attendanceCorrection->id)
                ->where('status', false)
                ->update([
                    'status' => true,
                    'reviewed_admin_id' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

            if ($updatedRows === 0) {
                return;
            }

            $attendance = $attendanceCorrection->attendance;

            $attendance->update([
                'clock_in_at' => $attendanceCorrection->requested_clock_in_at,
                'clock_out_at' => $attendanceCorrection->requested_clock_out_at,
                'notes' => $attendanceCorrection->requested_notes,
            ]);

            $attendance->breakTimes()->delete();

            $workDate = $attendance->work_date;
            $workDateTime = Carbon::parse($workDate)->format('Y-m-d');

            foreach ($attendanceCorrection->requested_breaks as $breakDate) {
                $breakStart = $breakDate['start'];
                $breakEnd = $breakDate['end'];

                if (empty($breakStart) || empty($breakEnd)) {
                    continue;
                }

                $breakStartDateTime = $workDateTime . ' ' . $breakStart;
                $breakEndDateTime = $workDateTime . ' ' . $breakEnd;
                $breakStartDateTimeCarbon = Carbon::createFromFormat('Y-m-d H:i', $breakStartDateTime);
                $breakEndDateTimeCarbon = Carbon::createFromFormat('Y-m-d H:i', $breakEndDateTime);

                $attendance->breakTimes()->create([
                    'break_start_at' => $breakStartDateTimeCarbon,
                    'break_end_at' => $breakEndDateTimeCarbon,
                ]);
            }

        });

        return redirect()->route('admin.requests.show', ['attendanceCorrection' => $attendanceCorrection->id]);

    }
}
