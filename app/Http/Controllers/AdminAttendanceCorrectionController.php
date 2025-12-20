<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCorrection;
use Illuminate\Http\Request;

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

        // // 申請データを降順で取得
        $attendanceCorrections = $attendanceCorrectionQuery->orderByDesc('created_at')->get();

        return view('admin.request.admin_request_list', compact('headerType', 'attendanceCorrections', 'page'));
    }

    public function show(AttendanceCorrection $attendanceCorrection)
    {
        $headerType = 'admin';
        $attendanceCorrection->load(['attendance.user', 'attendance.breakTimes',]);

        return view('admin.request.admin_approval', compact('headerType', 'attendanceCorrection'));
    }
}
