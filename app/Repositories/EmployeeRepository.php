<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data abstraction for employees
 *
 * @package    BuildDesk
 * @author     Rohit
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmployeeRepository
{
    /**
     * The employee model instance.
     */
    protected $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * get all rows
     * @param int|null $id the id of the row
     * @return object
     */
    public function rows($id = null)
    {
        $query = $this->employee->newQuery();

        if ($id) {
            $query->where('id', $id);
        }

        // filter by organisation
        if (request()->filled('filter_organisation_id')) {
            $query->where('organisation_id', request('filter_organisation_id'));
        }

        return $query;
    }

    public function createEmployee(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Create User
            $user = User::create([
                'name'            => $data['name'],
                'email'           => $data['email'],
                'password'        => bcrypt($data['password']),
                'role'            => 'employee',
                'organisation_id' => $data['organisation_id'] ?? auth()->user()->organisation_id,
            ]);

            // 2. Create Employee Profile
            return $this->employee->create([
                'user_id'               => $user->id,
                'employee_code'         => $data['employee_code'],
                'designation'           => $data['designation'] ?? null,
                'joining_date'          => $data['joining_date'] ?? null,
                'salary'                => $data['salary'] ?? 0,
                'auto_generate_salary'  => isset($data['auto_generate_salary']) && $data['auto_generate_salary'] ? true : false,
                'organisation_id'       => $user->organisation_id,
            ]);
        });
    }

    public function updateEmployee($employee, array $data)
    {
        return DB::transaction(function () use ($employee, $data) {
            // Update User
            $employee->user->update([
                'name'  => $data['name'],
                'email' => $data['email'],
            ]);

            if (!empty($data['password'])) {
                $employee->user->update(['password' => bcrypt($data['password'])]);
            }

            // Update Employee Profile
            $employee->update([
                'employee_code'         => $data['employee_code'],
                'designation'           => $data['designation'] ?? null,
                'joining_date'          => $data['joining_date'] ?? null,
                'salary'                => $data['salary'] ?? 0,
                'auto_generate_salary'  => isset($data['auto_generate_salary']) && $data['auto_generate_salary'] ? true : false,
            ]);

            return $employee;
        });
    }

    public function deleteEmployee($employee)
    {
        return DB::transaction(function () use ($employee) {
            $employee->delete();
        });
    }
}
