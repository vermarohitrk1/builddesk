<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display the main tabs (Tab 1: Take Attendance, Tab 2: Attendance Records)
     */
    public function index()
    {
        $orgId = auth()->user()->organisation_id;
        $employees = Employee::where('organisation_id', $orgId)->with('user')->orderBy('id')->get();
        return view('pages.hrm.attendance.index', compact('employees'));
    }

    /**
     * AJAX endpoint to render the Daily Attendance Form
     * Receives requested date, fetches active employees and any existing attendance overlay.
     */
    public function getDaily(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        
        $orgId = auth()->user()->organisation_id;
        $targetDate = Carbon::parse($request->date)->format('Y-m-d');

        // Fetch active employees (we assume all in DB are active currently)
        $employees = Employee::where('organisation_id', $orgId)
                            ->with('user')
                            ->orderBy('id')
                            ->get();

        // Fetch existing attendance records if any for the requested date
        $attendances = Attendance::where('organisation_id', $orgId)
                                 ->where('attendance_date', $targetDate)
                                 ->get()
                                 ->keyBy('employee_id');

        $html = view('pages.hrm.attendance.partials.daily_table', compact('employees', 'attendances', 'targetDate'))->render();

        return $this->ajaxResponse('success', '', ['html' => $html]);
    }

    /**
     * AJAX endpoint to save/update daily attendance bulk list
     */
    public function saveDaily(Request $request)
    {
        $rules = [
            'attendance_date' => 'required|date',
            'attendance'      => 'required|array',
            'attendance.*.status' => 'required|in:present,half_day,leave,absent',
            'attendance.*.remarks' => 'nullable|string',
        ];

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $orgId = auth()->user()->organisation_id;
        $targetDate = Carbon::parse($request->attendance_date)->format('Y-m-d');

        foreach ($request->attendance as $employeeId => $data) {
            Attendance::updateOrCreate(
                [
                    'organisation_id' => $orgId,
                    'employee_id' => $employeeId,
                    'attendance_date' => $targetDate,
                ],
                [
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? null,
                ]
            );
        }

        return $this->ajaxResponse('success', 'Attendance for ' . Carbon::parse($targetDate)->format('d M Y') . ' saved successfully.');
    }

    /**
     * AJAX endpoint to render the Monthly Matrix
     */
    public function getMatrix(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer',
            'employee_id' => 'nullable|exists:employees,id'
        ]);

        $orgId = auth()->user()->organisation_id;
        $month = (int) $request->month;
        $year  = (int) $request->year;
        
        $employeeId = $request->employee_id;

        // Number of days in requested month
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Query Employees
        $query = Employee::where('organisation_id', $orgId)->with('user')->orderBy('id');
        if ($employeeId) {
            $query->where('id', $employeeId);
        }
        $employees = $query->get();

        // Load attendances for this month (across all requested employees)
        $attendances = Attendance::where('organisation_id', $orgId)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year);
            
        if ($employeeId) {
            $attendances->where('employee_id', $employeeId);
        }
        
        $attendanceData = $attendances->get();

        // Build a structured data matrix to pass into View easily
        // Format: matrix[employeeId][day] = status
        $matrix = [];
        $summary = [];

        foreach ($employees as $emp) {
            $matrix[$emp->id] = [];
            $summary[$emp->id] = ['present' => 0, 'half_day' => 0, 'leave' => 0, 'absent' => 0];
        }

        foreach ($attendanceData as $record) {
            $day = Carbon::parse($record->attendance_date)->day;
            
            if (isset($matrix[$record->employee_id])) {
                $matrix[$record->employee_id][$day] = $record->status;
                
                // Keep tally
                $summary[$record->employee_id][$record->status]++;
            }
        }

        $html = view('pages.hrm.attendance.partials.matrix_table', compact('employees', 'daysInMonth', 'matrix', 'summary', 'month', 'year'))->render();

        return $this->ajaxResponse('success', '', ['html' => $html]);
    }
}
