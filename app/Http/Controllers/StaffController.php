<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::where('is_admin', 0)->get();

        return view('admin.staff.staff_list', [
            'headerType' => 'admin',
            'users' => $users,
        ]);
    }

    public function attendance($id)
    {
        $headerType = 'admin';

        return view('admin.staff.staff_attendance', [
            'headerType' => 'admin',
            'staffId' => $id,
        ]);
    }
}
