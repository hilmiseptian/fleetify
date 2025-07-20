<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceHistory;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,employee_id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $employeeId = $request->employee_id;
        $today = now()->toDateString();

        $alreadyClockedIn = Attendance::where('employee_id', $employeeId)
            ->whereDate('clock_in', $today)
            ->exists();

        if ($alreadyClockedIn) {
            return response()->json([
                'message' => 'You have already clocked in today.',
            ], 409); // 409 Conflict
        }

        $now = now();

        $attendance = Attendance::create([
            'employee_id' => $request->employee_id,
            'attendance_id' => uniqid("ATT-"),
            'clock_in' => $now,
        ]);

        AttendanceHistory::create([
            'employee_id' => $request->employee_id,
            'attendance_id' => $attendance->attendance_id,
            'date_attendance' => $now,
            'attendance_type' => 1, // 1 = In
            'description' => 'Clock-in',
        ]);

        return response()->json($attendance, 201);
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
        ]);

        $employeeId = $request->employee_id;
        $today = now()->toDateString();

        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereDate('clock_in', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'You have not clocked in today.',
            ], 409); // 409 Conflict
        }

        if ($attendance->clock_out) {
            return response()->json([
                'message' => 'You have already clocked out today.',
            ], 409); // 409 Conflict
        }

        $attendance->update(['clock_out' => now()]);

        AttendanceHistory::create([
            'employee_id' => $employeeId,
            'attendance_id' => $attendance->attendance_id,
            'date_attendance' => now(),
            'attendance_type' => 2, // 2 = Out
            'description' => 'Clock-out',
        ]);

        return response()->json(['message' => 'Clocked out successfully.']);
    }

    public function report(Request $request)
    {
        $query = Attendance::with(['employee.department'])
            ->when($request->filled('date'), fn($q) => $q->whereDate('clock_in', $request->date))
            ->when(
                $request->filled('department_id'),
                fn($q) =>
                $q->whereHas(
                    'employee',
                    fn($q2) =>
                    $q2->where('department_id', $request->department_id)
                )
            );

        $records = $query->get()->map(function ($record) {
            $clockInLate = optional($record->employee->department)->max_clock_in_time &&
                date('H:i:s', strtotime($record->clock_in)) > $record->employee->department->max_clock_in_time;

            $clockOutEarly = optional($record->employee->department)->max_clock_out_time &&
                $record->clock_out &&
                date('H:i:s', strtotime($record->clock_out)) < $record->employee->department->max_clock_out_time;

            return [
                'employee' => $record->employee->name,
                'department' => $record->employee->department->department_name,
                'clock_in' => $record->clock_in,
                'clock_out' => $record->clock_out,
                'late' => $clockInLate,
                'left_early' => $clockOutEarly,
            ];
        });

        return response()->json($records);
    }
}
