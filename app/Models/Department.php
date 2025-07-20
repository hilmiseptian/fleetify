<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = [
        'department_name',
        'max_clock_in_time',
        'max_clock_out_time',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
