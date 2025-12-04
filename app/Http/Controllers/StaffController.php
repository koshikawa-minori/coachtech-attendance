<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        return view('admin.staff.index', [
            'headerType' => 'admin',
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
