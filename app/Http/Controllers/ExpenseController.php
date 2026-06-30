<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('pages.expenses.index');
    }

    public function getExpensesData()
    {
        $orgId = Auth::user()->organisation_id;
        $query = Expense::where('organisation_id', $orgId)
            ->with(['category', 'supplier', 'creator']);

        return DataTables::of($query)
            ->addColumn('date', function ($expense) {
                return $expense->expense_date->format('d M Y');
            })
            ->addColumn('category', function ($expense) {
                return $expense->category ? $expense->category->name : 'N/A';
            })
            ->addColumn('supplier', function ($expense) {
                return $expense->supplier ? $expense->supplier->name : '<span class="text-muted">None</span>';
            })
            ->addColumn('payment_method_badge', function ($expense) {
                $badges = [
                    'cash' => 'bg-success',
                    'bank' => 'bg-primary',
                    'upi' => 'bg-info',
                    'card' => 'bg-warning text-dark',
                    'cheque' => 'bg-secondary'
                ];
                $class = $badges[$expense->payment_method] ?? 'bg-light text-dark';
                return '<span class="badge ' . $class . '">' . strtoupper($expense->payment_method) . '</span>';
            })
            ->addColumn('amount_formatted', function ($expense) {
                return '₹ ' . number_format($expense->amount, 2);
            })
            ->addColumn('created_by_name', function ($expense) {
                return $expense->creator ? $expense->creator->name : 'System';
            })
            ->addColumn('actions', function ($expense) {
                $editUrl = route('expenses.edit', $expense->id);
                $destroyUrl = route('expenses.destroy', $expense->id);
                
                $attachmentLink = '';
                if ($expense->attachment) {
                    $attachmentLink = '<a href="' . asset('storage/' . $expense->attachment) . '" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="View Attachment"><i class="fas fa-paperclip"></i></a>';
                }

                return '<div class="btn-group">' . 
                    $attachmentLink .
                    '<button class="btn btn-sm btn-outline-primary modal-trigger me-1" data-url="' . $editUrl . '" data-title="Edit Expense" data-size="modal-md"><i class="fas fa-edit"></i></button>' .
                    '<a href="' . $destroyUrl . '" class="btn btn-sm btn-outline-danger ajax-link" data-method="DELETE" data-confirm="Are you sure you want to delete this expense?"><i class="fas fa-trash"></i></a>' .
                    '</div>';
            })
            ->rawColumns(['supplier', 'payment_method_badge', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $orgId = Auth::user()->organisation_id;
        $categories = ExpenseCategory::where('organisation_id', $orgId)->orderBy('name')->get();
        $suppliers = Supplier::where('organisation_id', $orgId)->orderBy('name')->get();

        $html = view('pages.expenses.create', compact('categories', 'suppliers'))->render();

        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Record Expense'
        ]);
    }

    public function store(Request $request)
    {
        $orgId = Auth::user()->organisation_id;

        $validator = Validator::make($request->all(), [
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,upi,card,cheque',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('expense_attachments', 'public');
        }

        Expense::create([
            'organisation_id' => $orgId,
            'expense_category_id' => $request->expense_category_id,
            'supplier_id' => $request->supplier_id,
            'expense_date' => $request->expense_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'description' => $request->description,
            'attachment' => $attachmentPath,
            'created_by' => Auth::id(),
        ]);

        return $this->ajaxResponse('success', 'Expense recorded successfully!', [
            'close_modal' => true,
            'reload_table' => 'expenses-table',
        ]);
    }

    public function edit($id)
    {
        $orgId = Auth::user()->organisation_id;
        $expense = Expense::where('organisation_id', $orgId)->findOrFail($id);
        $categories = ExpenseCategory::where('organisation_id', $orgId)->orderBy('name')->get();
        $suppliers = Supplier::where('organisation_id', $orgId)->orderBy('name')->get();

        $html = view('pages.expenses.edit', compact('expense', 'categories', 'suppliers'))->render();

        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Edit Expense'
        ]);
    }

    public function update(Request $request, $id)
    {
        $orgId = Auth::user()->organisation_id;
        $expense = Expense::where('organisation_id', $orgId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'expense_date' => 'required|date',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,upi,card,cheque',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $attachmentPath = $expense->attachment;
        if ($request->hasFile('attachment')) {
            if ($expense->attachment) {
                Storage::disk('public')->delete($expense->attachment);
            }
            $attachmentPath = $request->file('attachment')->store('expense_attachments', 'public');
        }

        $expense->update([
            'expense_category_id' => $request->expense_category_id,
            'supplier_id' => $request->supplier_id,
            'expense_date' => $request->expense_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'description' => $request->description,
            'attachment' => $attachmentPath,
            'updated_by' => Auth::id(),
        ]);

        return $this->ajaxResponse('success', 'Expense updated successfully!', [
            'close_modal' => true,
            'reload_table' => 'expenses-table',
        ]);
    }

    public function destroy($id)
    {
        $orgId = Auth::user()->organisation_id;
        $expense = Expense::where('organisation_id', $orgId)->findOrFail($id);

        if ($expense->attachment) {
            Storage::disk('public')->delete($expense->attachment);
        }

        $expense->delete();

        return $this->ajaxResponse('success', 'Expense deleted successfully!', [
            'reload_table' => 'expenses-table',
        ]);
    }
}
