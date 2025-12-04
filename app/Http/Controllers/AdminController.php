<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.attendance.admin_attendance_list', [
            'headerType' => 'admin',
        ]);
    }

    public function show(Attendance $attendance){
        return view('admin.attendance.admin_attendance_detail.blade', [
        'headerType' => 'admin',
        'attendance' => $attendance,
    ]);
    }
}
