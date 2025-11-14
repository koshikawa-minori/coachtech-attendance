<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AttendanceCorrection;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'password',
    ];

    public function attendanceCorrections()
    {
        return $this->hasMany(AttendanceCorrection::class, 'reviewed_admin_id');
    }
}
