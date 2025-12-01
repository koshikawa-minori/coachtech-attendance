<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceCorrectionController extends Controller
{
    public function index(Request $request) {
        $headerType = 'user';

        return view('request.request_list', compact('headerType'));
    }
}
