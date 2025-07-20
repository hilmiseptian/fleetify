<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    // GET /api/employees
    public function index()
    {
        return response()->json(Employee::with('department')->get());
    }

    // POST /api/employees
    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'employee_id' => 'required|string|unique:employees,employee_id',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $employee = Employee::create([
            'employee_id' => $request->employee_id,
            'name' => $request->name,
            'address' => $request->address,
            'department_id' => $request->department_id
        ]);

        return response()->json($employee, 201);
    }

    // GET /api/employees/{id}
    public function show($id)
    {
        $employee = Employee::with('department')->findOrFail($id);
        return response()->json($employee);
    }

    // PUT /api/employees/{id}
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validator =  Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $employee->update([
            'name' => $request->name,
            'address' => $request->address,
            'department_id' => $request->department_id,
        ]);

        return response()->json($employee);
    }

    // DELETE /api/employees/{id}
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json(null, 204);
    }
}
