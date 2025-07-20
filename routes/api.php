<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceHistoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('departments', DepartmentController::class);
Route::apiResource('employees', EmployeeController::class);
Route::post('attendance/clock-in', [AttendanceController::class, 'clockIn']);
Route::put('attendance/clock-out', [AttendanceController::class, 'clockOut']);
Route::get('attendance/report', [AttendanceController::class, 'report']);
