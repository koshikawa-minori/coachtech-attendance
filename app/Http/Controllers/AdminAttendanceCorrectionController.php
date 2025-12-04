<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAttendanceCorrectionController extends Controller
{
    public function index()
    {
        return view('admin.requests.index', [
            'headerType' => 'admin',
        ]);
    }
}
