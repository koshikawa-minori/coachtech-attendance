<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;

class AttendanceCorrection extends Model
{
    use HasFactory;

    protected $table = 'attendance_requests';

    protected $casts = [
        'requested_clock_in_at' => 'datetime',
        'requested_clock_out_at' => 'datetime',
        'requested_breaks' => 'array',
        'status' => 'boolean',
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
        return $this->belongsTo(User::class, 'reviewed_admin_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function approveByAdmin(int $adminId): void
    {
        DB::transaction(function () use ($adminId) {

            // 同時押し対策
            $updatedRows = AttendanceCorrection::where('id', $this->id)
                ->where('status', false)
                ->update([
                    'status' => true,
                    'reviewed_admin_id' => $adminId,
                    'reviewed_at' => now(),
                ]);

            if ($updatedRows === 0) {
                return;
            }

            $attendance = $this->attendance;

            $attendance->update([
                'clock_in_at' => $this->requested_clock_in_at,
                'clock_out_at' => $this->requested_clock_out_at,
                'notes' => $this->requested_notes,
            ]);

            $attendance->breakTimes()->delete();

            $workDate = $attendance->work_date;
            $workDateTime = Carbon::parse($workDate)->format('Y-m-d');

            foreach ($this->requested_breaks as $breakDate) {
                $breakStart = $breakDate['start'] ?? null;
                $breakEnd = $breakDate['end'] ?? null;

                if (empty($breakStart) || empty($breakEnd)) {
                    continue;
                }

                $breakStartDateTimeCarbon = Carbon::createFromFormat('Y-m-d H:i', $workDateTime . ' ' . $breakStart);
                $breakEndDateTimeCarbon = Carbon::createFromFormat('Y-m-d H:i', $workDateTime . ' ' . $breakEnd);

                $attendance->breakTimes()->create([
                    'break_start_at' => $breakStartDateTimeCarbon,
                    'break_end_at' => $breakEndDateTimeCarbon,
                ]);
            }

        });
    }
}
