<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceHistory extends Model
{
    use HasFactory;

    protected $table = 'attendance_histories';

    protected $fillable = [
        'employee_id',
        'attendance_id',
        'date_attendance',
        'attendance_type',
        'description',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id', 'attendance_id');
    }
}
