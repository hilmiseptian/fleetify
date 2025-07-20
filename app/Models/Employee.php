<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'employees';

    protected $fillable = [
        'employee_id',
        'department_id',
        'name',
        'address',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id', 'employee_id');
    }

    public function attendanceHistories()
    {
        return $this->hasMany(AttendanceHistory::class, 'employee_id', 'employee_id');
    }
}
