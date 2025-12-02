<?php

namespace App\Http\Controllers;

use App\Models\AttendanceCorrection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceCorrectionController extends Controller
{
    public function index(Request $request) {
        $headerType = 'user';
        $userId = Auth::id();
        // ログインユーザーの申請だけに絞る
        $attendanceCorrectionQuery = AttendanceCorrection::whereHas('attendance', function($query) use ($userId) {
            $query->where('user_id', $userId);
        });

        $page = $request->query('page', 'wait');

        if ($page === 'done') {
            $attendanceCorrectionQuery->where('status', true);
        } else {
            $attendanceCorrectionQuery->where('status', false);
        }

        // 申請データを降順で取得
        $attendanceCorrections = $attendanceCorrectionQuery->orderByDesc('created_at')->get();

        return view('request.request_list', compact('headerType', 'attendanceCorrections', 'page'));
    }
}
