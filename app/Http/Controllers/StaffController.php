<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
        return view('admin.staff.attendance', [
            'headerType' => 'admin',
            'staffId' => $id,
        ]);
    }
}
