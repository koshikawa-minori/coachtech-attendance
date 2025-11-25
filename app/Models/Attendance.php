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

    protected $casts = [
        'work_date' => 'date',
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function attendanceCorrection()
    {
        return $this->hasOne(AttendanceCorrection::class);
    }

    // 勤務合計時間の計算
    public function calculateWorkingMinutes()
    {
        // 勤務分計算
        $workStart = $this->clock_in_at;
        $workEnd = $this->clock_out_at;

        if (is_null($workStart) || is_null($workEnd)) {
            return null;
        }

        $workMinutes = $workStart->diffInMinutes($workEnd);
        $breakMinutes = $this->calculateBreakMinutes();

        $workingMinutes = $workMinutes - $breakMinutes;

        return $workingMinutes;
    }

    // 休憩合計時間の計算
    public function calculateBreakMinutes()
    {
        // 休憩時間計算
        $breakMinutes = 0;

        foreach ($this->breakTimes as $break) {
            $breakStart = $break->break_start_at;
            $breakEnd = $break->break_end_at;

            if (is_null($breakStart) || is_null($breakEnd)) {
                continue;
            }

            $breakMinutes += $breakStart->diffInMinutes($breakEnd);
        }

        return $breakMinutes;
    }

    // 勤務合計表示
    public function getTheTotalWorkAttribute()
    {
        $minutes = $this->calculateWorkingMinutes();

        if (is_null($minutes)) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $remainMinutes = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $remainMinutes);
    }

    // 休憩合計表示
    public function getTheTotalBreakAttribute()
    {
        $minutes = $this->calculateBreakMinutes();

        if ($minutes <= 0) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $remainMinutes = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $remainMinutes);
    }

}
