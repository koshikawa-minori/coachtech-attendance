<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\BreakTime;
use App\Models\AttendanceCorrection;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in_at',
        'clock_out_at',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function break_times()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function attendanceCorrection()
    {
        return $this->hasOne(AttendanceCorrection::class);
    }

}
