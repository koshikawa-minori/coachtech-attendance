<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Admin;
use App\Models\Attendance;

class AttendanceCorrection extends Model
{
    use HasFactory;

    protected $table = 'attendance_requests';

    protected $casts = [
    'requested_breaks' => 'array',
    ];

    protected $fillable = [
        'attendance_id',
        'requested_clock_in_at',
        'requested_clock_out_at',
        'requested_breaks',
        'requested_notes',
        'status',
        'reviewed_admin_id',
        'reviewed_at',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'reviewed_admin_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
