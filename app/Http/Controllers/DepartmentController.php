<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    // GET /api/departments
    public function index()
    {
        return response()->json(Department::all());
    }

    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'department_name' => 'required|string|max:255',
            'max_clock_in_time' => 'required|date_format:H:i:s',
            'max_clock_out_time' => 'required|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $department = Department::create([
            'department_name' => $request->department_name,
            'max_clock_in_time' => $request->max_clock_in_time,
            'max_clock_out_time' => $request->max_clock_out_time,
        ]);

        return response()->json($department, 201);
    }

    // GET /api/departments/{id}
    public function show($id)
    {
        $department = Department::findOrFail($id);
        return response()->json($department);
    }

    // PUT /api/departments/{id}
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validator =  Validator::make($request->all(), [
            'department_name' => 'required|string|max:255',
            'max_clock_in_time' => 'required|date_format:H:i:s',
            'max_clock_out_time' => 'required|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $department->update([
            'department_name' => $request->department_name,
            'max_clock_in_time' => $request->max_clock_in_time,
            'max_clock_out_time' => $request->max_clock_out_time,
        ]);

        return response()->json($department);
    }

    // DELETE /api/departments/{id}
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json(null, 204);
    }
}
