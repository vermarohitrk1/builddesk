<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PayrollController extends Controller
{
    /**
     * Index page with filters
     */
    public function index()
    {
        $orgId = auth()->user()->organisation_id;
        $employees = Employee::where('organisation_id', $orgId)->with('user')->orderBy('id')->get();

        return view('pages.hrm.payrolls.index', compact('employees'));
    }

    /**
     * DataTable data endpoint
     */
    public function getData(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        $query = Payroll::where('organisation_id', $orgId)
            ->with(['employee.user', 'paidBy', 'createdBy']);

        // Filters
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->filled('payroll_type')) {
            $query->where('payroll_type', $request->payroll_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('month')) {
            $query->where('payroll_month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('payroll_year', $request->year);
        }

        return DataTables::of($query)
            ->addColumn('employee_name', fn($p) => $p->employee?->user?->name ?? 'N/A')
            ->addColumn('month_year', fn($p) => Carbon::create()->month($p->payroll_month)->format('F') . ' ' . $p->payroll_year)
            ->addColumn('amount_formatted', fn($p) => '₹ ' . number_format($p->amount, 2))
            ->addColumn('status_badge', function ($p) {
                $colors = [
                    'generated' => 'bg-warning text-dark',
                    'paid'      => 'bg-success',
                    'cancelled' => 'bg-secondary',
                ];
                $class = $colors[$p->status] ?? 'bg-light text-dark';
                return '<span class="badge ' . $class . '">' . ucfirst($p->status) . '</span>';
            })
            ->addColumn('paid_at_formatted', fn($p) => $p->paid_at ? $p->paid_at->format('d M Y h:i A') : '-')
            ->addColumn('payroll_date_formatted', fn($p) => $p->payroll_date ? $p->payroll_date->format('d M Y') : '-')
            ->addColumn('actions', function ($p) {
                $btn = '';
                if ($p->status === 'generated') {
                    $btn .= '<button class="btn btn-sm btn-danger cancel-payroll" data-id="' . $p->id . '">
                        <i class="fas fa-times"></i> Cancel
                    </button>';
                }
                return $btn;
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    /**
     * Show generate payroll modal
     */
    public function showGenerateModal()
    {
        $orgId = auth()->user()->organisation_id;
        $employees = Employee::where('organisation_id', $orgId)->with('user')->orderBy('id')->get();

        $html = view('pages.hrm.payrolls.generate', compact('employees'))->render();

        return $this->ajaxResponse('success', '', [
            'html'  => $html,
            'title' => 'Generate Payroll',
        ]);
    }

    /**
     * Generate payroll records
     */
    public function generate(Request $request)
    {
        $rules = [
            'employee_ids'   => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'payroll_type'   => 'required|in:salary,bonus,incentive,advance,reimbursement,other',
            'payroll_month'  => 'required|integer|between:1,12',
            'payroll_year'   => 'required|integer|min:2020',
            'payroll_date'   => 'required|date',
            'remarks'        => 'nullable|string',
        ];

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $orgId = auth()->user()->organisation_id;
        $userId = auth()->id();
        $generated = 0;
        $skipped = 0;

        foreach ($request->employee_ids as $empId) {
            $employee = Employee::where('organisation_id', $orgId)->findOrFail($empId);

            // Duplicate check
            $exists = Payroll::where('organisation_id', $orgId)
                ->where('employee_id', $empId)
                ->where('payroll_type', $request->payroll_type)
                ->where('payroll_month', $request->payroll_month)
                ->where('payroll_year', $request->payroll_year)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            Payroll::create([
                'organisation_id' => $orgId,
                'employee_id'     => $empId,
                'payroll_type'    => $request->payroll_type,
                'payroll_month'   => $request->payroll_month,
                'payroll_year'    => $request->payroll_year,
                'payroll_date'    => $request->payroll_date,
                'amount'          => $employee->salary,
                'status'          => 'generated',
                'remarks'         => $request->remarks,
                'created_by'      => $userId,
            ]);

            $generated++;
        }

        $message = "{$generated} payroll record(s) generated.";
        if ($skipped > 0) {
            $message .= " {$skipped} skipped (already exists).";
        }

        return $this->ajaxResponse('success', $message, [
            'close_modal'  => true,
            'reload_table' => 'payrolls-table',
        ]);
    }

    /**
     * Mark selected payroll records as Paid and auto-create Expense entries
     */
    public function markPaid(Request $request)
    {
        $rules = [
            'ids'            => 'required|array|min:1',
            'ids.*'          => 'integer',
            'payment_method' => 'required|in:upi,card,cheque,bank,cash',
        ];

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $orgId  = auth()->user()->organisation_id;
        $userId = auth()->id();
        $now    = Carbon::now();

        // Get Salary expense category for this org
        $salaryCategory = ExpenseCategory::where('organisation_id', $orgId)
            ->whereRaw('LOWER(name) = ?', ['salary'])
            ->first();

        $payrolls = Payroll::where('organisation_id', $orgId)
            ->whereIn('id', $request->ids)
            ->where('status', 'generated')
            ->with('employee.user')
            ->get();

        if ($payrolls->isEmpty()) {
            return $this->ajaxResponse('error', 'No eligible payroll records found (only "Generated" records can be marked paid).');
        }

        foreach ($payrolls as $payroll) {
            // Update payroll status
            $payroll->update([
                'status'  => 'paid',
                'paid_by' => $userId,
                'paid_at' => $now,
            ]);

            // Auto-create Expense for EVERY payroll type when marked as paid
            if ($salaryCategory) {
                $empName   = $payroll->employee?->user?->name ?? 'Employee';
                $monthName = Carbon::create()->month($payroll->payroll_month)->format('F');
                $description = ucfirst($payroll->payroll_type) . ' - ' . $empName . ' - ' . $monthName . ' ' . $payroll->payroll_year;

                Expense::create([
                    'organisation_id'     => $orgId,
                    'expense_category_id' => $salaryCategory->id,
                    'supplier_id'         => null,
                    'expense_date'        => $payroll->payroll_date,
                    'amount'              => $payroll->amount,
                    'payment_method'      => $request->payment_method,
                    'description'         => $description,
                    'created_by'          => $userId,
                    'updated_by'          => $userId,
                ]);
            }
        }

        $count = $payrolls->count();
        return $this->ajaxResponse('success', "{$count} payroll record(s) marked as Paid. Expenses logged automatically.", [
            'reload_table' => 'payrolls-table',
        ]);
    }

    /**
     * Cancel a payroll record
     */
    public function cancel(Request $request, $id)
    {
        $orgId   = auth()->user()->organisation_id;
        $payroll = Payroll::where('organisation_id', $orgId)->findOrFail($id);

        if ($payroll->status !== 'generated') {
            return $this->ajaxResponse('error', 'Only "Generated" records can be cancelled.');
        }

        $payroll->update(['status' => 'cancelled']);

        return $this->ajaxResponse('success', 'Payroll cancelled.', [
            'reload_table' => 'payrolls-table',
        ]);
    }

    /**
     * Export payroll records as CSV
     */
    public function export(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        $query = Payroll::where('organisation_id', $orgId)
            ->with(['employee.user', 'paidBy']);

        if ($request->filled('employee_id')) $query->where('employee_id', $request->employee_id);
        if ($request->filled('payroll_type'))  $query->where('payroll_type', $request->payroll_type);
        if ($request->filled('status'))        $query->where('status', $request->status);
        if ($request->filled('month'))         $query->where('payroll_month', $request->month);
        if ($request->filled('year'))          $query->where('payroll_year', $request->year);

        $payrolls = $query->orderBy('payroll_year', 'desc')->orderBy('payroll_month', 'desc')->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=payroll_' . now()->format('Ymd_His') . '.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($payrolls) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employee', 'Type', 'Month', 'Year', 'Amount', 'Status', 'Payroll Date', 'Paid By', 'Paid At']);
            foreach ($payrolls as $p) {
                $monthName = Carbon::create()->month($p->payroll_month)->format('F');
                fputcsv($file, [
                    $p->employee?->user?->name ?? 'N/A',
                    ucfirst($p->payroll_type),
                    $monthName,
                    $p->payroll_year,
                    $p->amount,
                    ucfirst($p->status),
                    $p->payroll_date?->format('d M Y'),
                    $p->paidBy?->name ?? '-',
                    $p->paid_at?->format('d M Y h:i A') ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
