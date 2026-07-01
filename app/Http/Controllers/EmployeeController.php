<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Services\EmployeeService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        return view('pages.hrm.employees.index');
    }

    public function getEmployeesData()
    {
        $query = \App\Models\Employee::with('user');

        return DataTables::of($query)
            ->addColumn('name', function ($employee) {
                return $employee->user->name;
            })
            ->addColumn('email', function ($employee) {
                return $employee->user->email;
            })
            ->addColumn('actions', function ($employee) {
                return '<button class="btn btn-sm btn-primary modal-trigger" data-url="' . route('employees.edit', $employee->id) . '" data-title="Edit Employee"><i class="fas fa-edit"></i> Edit</button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        $html = view('pages.hrm.employees.create')->render();
        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Register Employee'
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:6',
            'employee_code'         => 'required|string|unique:employees,employee_code',
            'designation'           => 'nullable|string',
            'joining_date'          => 'nullable|date',
            'salary'                => 'nullable|numeric',
            'auto_generate_salary'  => 'nullable|boolean',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $validated = $validator->validated();
        
        $this->employeeService->createEmployee($validated);

        return $this->ajaxResponse('success', 'Employee registered successfully!', [
            'close_modal' => true,
            'reload_table' => 'employees-table',
        ]);
    }

    public function edit($id)
    {
        $orgId = auth()->user()->organisation_id;
        $employee = Employee::where('organisation_id', $orgId)->findOrFail($id);
        
        $html = view('pages.hrm.employees.edit', compact('employee'))->render();
        
        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Edit Employee'
        ]);
    }

    public function update(Request $request, $id)
    {
        $orgId = auth()->user()->organisation_id;
        $employee = Employee::where('organisation_id', $orgId)->findOrFail($id);

        $rules = [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . $employee->user_id,
            'employee_code'         => 'required|string|unique:employees,employee_code,' . $id,
            'designation'           => 'nullable|string',
            'joining_date'          => 'nullable|date',
            'salary'                => 'nullable|numeric',
            'auto_generate_salary'  => 'nullable|boolean',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $validated = $validator->validated();
        
        $this->employeeService->updateEmployee($employee, $validated);

        return $this->ajaxResponse('success', 'Employee updated successfully!', [
            'close_modal' => true,
            'reload_table' => 'employees-table',
        ]);
    }

    public function destroy($id)
    {
        $orgId = auth()->user()->organisation_id;
        $employee = Employee::where('organisation_id', $orgId)->findOrFail($id);
        
        $this->employeeService->deleteEmployee($employee);

        return $this->ajaxResponse('success', 'Employee deleted successfully!', [
            'reload_table' => 'employees-table',
        ]);
    }
}
