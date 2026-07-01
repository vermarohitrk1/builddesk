<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Get isolated queries for multi-tenancy
     */
    private function getBaseQueries($orgId)
    {
        return [
            'projects' => Project::where('organisation_id', $orgId)->with(['lead', 'lead.customer', 'lead.quotations', 'createdBy']),
            'expenses' => Expense::where('organisation_id', $orgId)->with(['category', 'supplier', 'creator'])
        ];
    }

    /**
     * 1. REVENUE REPORT
     */
    public function revenue(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        $data = $this->getRevenueData($request, $orgId);

        return view('pages.reports.revenue', [
            'reports' => $data['reports'],
            'totalRevenue' => $data['totalRevenue'],
            'projectCount' => $data['projectCount'],
            'filters' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'status' => $request->status ?? 'both'
            ]
        ]);
    }

    public function revenuePdf(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        $data = $this->getRevenueData($request, $orgId);

        $pdf = Pdf::loadView('pages.reports.pdf.revenue', [
            'reports' => $data['reports'],
            'totalRevenue' => $data['totalRevenue'],
            'projectCount' => $data['projectCount'],
            'date_from' => $request->date_from,
            'date_to' => $request->date_to
        ]);

        return $pdf->download('revenue_report_' . now()->format('Ymd_His') . '.pdf');
    }

    public function revenueExcel(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        $data = $this->getRevenueData($request, $orgId);

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=revenue_report_" . now()->format('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Project Number', 'Customer Name', 'Quotation Number', 'Completion Date', 'Revenue Amount', 'Created By']);
            foreach ($data['reports'] as $row) {
                fputcsv($file, [
                    $row['project_number'],
                    $row['customer_name'],
                    $row['quotation_number'],
                    $row['completion_date'],
                    $row['revenue_amount'],
                    $row['created_by']
                ]);
            }
            fputcsv($file, ['Total', '', '', '', $data['totalRevenue'], '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getRevenueData(Request $request, $orgId)
    {
        $queries = $this->getBaseQueries($orgId);
        $projectQuery = $queries['projects'];

        // Filter by started_at date range
        if ($request->filled('date_from')) {
            $projectQuery->whereDate('started_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $projectQuery->whereDate('started_at', '<=', $request->date_to);
        }

        $projects = $projectQuery->get();
        $reports = [];
        $totalRevenue = 0;

        foreach ($projects as $project) {
            $lead = $project->lead;
            if (!$lead) continue;

            // Get total quoted amount for the lead
            $quotations = $lead->quotations;
            $quotationNumbers = $quotations->pluck('quotation_number')->implode(', ') ?: 'N/A';
            $revenueAmount = $quotations->sum('total_amount');

            $reports[] = [
                'project_number' => $project->project_number,
                'customer_name' => $lead->customer ? $lead->customer->name : $lead->contact_name,
                'quotation_number' => $quotationNumbers,
                'completion_date' => $project->started_at ? $project->started_at->format('d M Y') : 'N/A',
                'revenue_amount' => $revenueAmount,
                'created_by' => $project->createdBy ? $project->createdBy->name : 'System'
            ];

            $totalRevenue += $revenueAmount;
        }

        return [
            'reports' => $reports,
            'totalRevenue' => $totalRevenue,
            'projectCount' => count($reports)
        ];
    }


    /**
     * 2. EXPENSE REPORT
     */
    public function expense(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        $data = $this->getExpenseData($request, $orgId);
        
        $categories = ExpenseCategory::where('organisation_id', $orgId)->orderBy('name')->get();
        $suppliers = Supplier::where('organisation_id', $orgId)->orderBy('name')->get();

        return view('pages.reports.expense', [
            'reports' => $data['reports'],
            'totalExpense' => $data['totalExpense'],
            'expenseCount' => $data['expenseCount'],
            'categories' => $categories,
            'suppliers' => $suppliers,
            'filters' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'category' => $request->category,
                'supplier' => $request->supplier,
                'payment_method' => $request->payment_method
            ]
        ]);
    }

    public function expensePdf(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        $data = $this->getExpenseData($request, $orgId);

        $pdf = Pdf::loadView('pages.reports.pdf.expense', [
            'reports' => $data['reports'],
            'totalExpense' => $data['totalExpense'],
            'date_from' => $request->date_from,
            'date_to' => $request->date_to
        ]);

        return $pdf->download('expense_report_' . now()->format('Ymd_His') . '.pdf');
    }

    public function expenseExcel(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        $data = $this->getExpenseData($request, $orgId);

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=expense_report_" . now()->format('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Expense Date', 'Category', 'Supplier', 'Amount', 'Payment Method', 'Description', 'Created By']);
            foreach ($data['reports'] as $row) {
                fputcsv($file, [
                    $row['expense_date'],
                    $row['category'],
                    $row['supplier'],
                    $row['amount'],
                    $row['payment_method'],
                    $row['description'],
                    $row['created_by']
                ]);
            }
            fputcsv($file, ['Total', '', '', $data['totalExpense'], '', '', '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getExpenseData(Request $request, $orgId)
    {
        $queries = $this->getBaseQueries($orgId);
        $expenseQuery = $queries['expenses'];

        if ($request->filled('date_from')) {
            $expenseQuery->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $expenseQuery->whereDate('expense_date', '<=', $request->date_to);
        }
        if ($request->filled('category')) {
            $expenseQuery->where('expense_category_id', $request->category);
        }
        if ($request->filled('supplier')) {
            $expenseQuery->where('supplier_id', $request->supplier);
        }
        if ($request->filled('payment_method')) {
            $expenseQuery->where('payment_method', $request->payment_method);
        }

        $expenses = $expenseQuery->orderBy('expense_date', 'desc')->get();
        $reports = [];
        $totalExpense = 0;

        foreach ($expenses as $expense) {
            $reports[] = [
                'expense_date' => $expense->expense_date->format('d M Y'),
                'category' => $expense->category ? $expense->category->name : 'N/A',
                'supplier' => $expense->supplier ? $expense->supplier->name : 'None',
                'amount' => $expense->amount,
                'payment_method' => strtoupper($expense->payment_method),
                'description' => $expense->description ?? 'N/A',
                'created_by' => $expense->creator ? $expense->creator->name : 'System'
            ];
            $totalExpense += $expense->amount;
        }

        return [
            'reports' => $reports,
            'totalExpense' => $totalExpense,
            'expenseCount' => count($reports)
        ];
    }


    /**
     * 3. CASH FLOW REPORT (FINANCIAL STATEMENT)
     */
    public function cashFlow(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        $data = $this->getCashFlowData($request, $orgId);

        return view('pages.reports.cash_flow', [
            'reports' => $data['reports'],
            'totalIn' => $data['totalIn'],
            'totalOut' => $data['totalOut'],
            'netProfit' => $data['netProfit'],
            'filters' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to
            ]
        ]);
    }

    public function cashFlowPdf(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        $data = $this->getCashFlowData($request, $orgId);

        $pdf = Pdf::loadView('pages.reports.pdf.cash_flow', [
            'reports' => $data['reports'],
            'totalIn' => $data['totalIn'],
            'totalOut' => $data['totalOut'],
            'netProfit' => $data['netProfit'],
            'date_from' => $request->date_from,
            'date_to' => $request->date_to
        ]);

        return $pdf->download('financial_statement_' . now()->format('Ymd_His') . '.pdf');
    }

    public function cashFlowExcel(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        $data = $this->getCashFlowData($request, $orgId);

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=financial_statement_" . now()->format('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Type', 'Reference', 'Description', 'Money In', 'Money Out', 'Running Balance']);
            foreach ($data['reports'] as $row) {
                fputcsv($file, [
                    $row['date_formatted'],
                    $row['type'],
                    $row['reference'],
                    $row['description'],
                    $row['money_in'] ? $row['money_in'] : '-',
                    $row['money_out'] ? $row['money_out'] : '-',
                    $row['balance']
                ]);
            }
            fputcsv($file, ['Total', '', '', '', $data['totalIn'], $data['totalOut'], $data['netProfit']]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getCashFlowData(Request $request, $orgId)
    {
        $queries = $this->getBaseQueries($orgId);

        // Fetch Projects (Revenue)
        $projectQuery = $queries['projects'];
        if ($request->filled('date_from')) {
            $projectQuery->whereDate('started_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $projectQuery->whereDate('started_at', '<=', $request->date_to);
        }
        $projects = $projectQuery->get();

        // Fetch Expenses
        $expenseQuery = $queries['expenses'];
        if ($request->filled('date_from')) {
            $expenseQuery->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $expenseQuery->whereDate('expense_date', '<=', $request->date_to);
        }
        $expenses = $expenseQuery->get();

        $rows = [];

        // Map Revenues
        foreach ($projects as $project) {
            $lead = $project->lead;
            if (!$lead) continue;
            
            $revenueAmount = $lead->quotations->sum('total_amount');
            $date = $project->started_at;

            $rows[] = [
                'date' => $date,
                'date_formatted' => $date->format('d M Y'),
                'type' => 'Revenue',
                'reference' => 'Project #' . $project->project_number,
                'description' => 'Revenue generated from ' . ($lead->customer ? $lead->customer->name : $lead->contact_name),
                'money_in' => $revenueAmount,
                'money_out' => 0
            ];
        }

        // Map Expenses
        foreach ($expenses as $expense) {
            $date = $expense->expense_date;

            $rows[] = [
                'date' => $date,
                'date_formatted' => $date->format('d M Y'),
                'type' => 'Expense',
                'reference' => 'Expense ID #' . $expense->id,
                'description' => ($expense->category ? $expense->category->name : 'General') . ' - ' . ($expense->description ?? 'No details'),
                'money_in' => 0,
                'money_out' => $expense->amount
            ];
        }

        // Sort chronologically by date
        usort($rows, function($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        // Compute running balance
        $balance = 0;
        $totalIn = 0;
        $totalOut = 0;

        foreach ($rows as &$row) {
            $balance += ($row['money_in'] - $row['money_out']);
            $row['balance'] = $balance;
            $totalIn += $row['money_in'];
            $totalOut += $row['money_out'];
        }

        return [
            'reports' => $rows,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'netProfit' => ($totalIn - $totalOut)
        ];
    }
}
