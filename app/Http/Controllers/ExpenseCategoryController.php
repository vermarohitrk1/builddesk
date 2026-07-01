<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ExpenseCategoryController extends Controller
{
    // Protected default category names that cannot be deleted
    const PROTECTED_NAMES = ['salary'];

    public function getData()
    {
        $orgId = auth()->user()->organisation_id;
        $query = ExpenseCategory::where('organisation_id', $orgId)->select(['id', 'name', 'created_at']);

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                $editBtn = '<button class="btn btn-sm btn-link p-0 text-primary modal-trigger" data-url="' . route('expenses.categories.edit', $row->id) . '" data-title="Edit Expense Category"><i class="fas fa-edit"></i></button>';
                $deleteBtn = '<a href="' . route('expenses.categories.destroy', $row->id) . '"
                    class="btn btn-sm btn-link text-danger p-0 ajax-link"
                    data-method="DELETE"
                    data-confirm="Are you sure you want to delete this category?">
                    <i class="fas fa-trash"></i>
                </a>';
                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $html = view('pages.expenses.categories.create')->render();
        return response()->json(['html' => $html]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $orgId = auth()->user()->organisation_id;

        // Prevent duplicate name per org
        $exists = ExpenseCategory::where('organisation_id', $orgId)
            ->whereRaw('LOWER(name) = ?', [strtolower($request->name)])
            ->exists();

        if ($exists) {
            return $this->ajaxResponse('error', 'A category with this name already exists.');
        }

        ExpenseCategory::create([
            'organisation_id' => $orgId,
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
        ]);

        return $this->ajaxResponse('success', 'Category created successfully.', [
            'close_modal' => true,
            'reload_table' => 'expense-categories-table',
        ]);
    }

    public function edit($id)
    {
        $orgId = auth()->user()->organisation_id;
        $category = ExpenseCategory::where('organisation_id', $orgId)->findOrFail($id);
        $html = view('pages.expenses.categories.edit', compact('category'))->render();
        return response()->json(['html' => $html]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:100']);

        $orgId = auth()->user()->organisation_id;
        $category = ExpenseCategory::where('organisation_id', $orgId)->findOrFail($id);

        // Cannot rename a protected category
        if (in_array(strtolower($category->name), self::PROTECTED_NAMES)) {
            return $this->ajaxResponse('error', '"' . $category->name . '" is a system category and cannot be renamed.');
        }

        $category->update([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
        ]);

        return $this->ajaxResponse('success', 'Category updated successfully.', [
            'close_modal' => true,
            'reload_table' => 'expense-categories-table',
        ]);
    }

    public function destroy($id)
    {
        $orgId = auth()->user()->organisation_id;
        $category = ExpenseCategory::where('organisation_id', $orgId)->findOrFail($id);

        // Block deletion of system default categories (e.g. "Salary" used in payroll)
        if (in_array(strtolower($category->name), self::PROTECTED_NAMES)) {
            return $this->ajaxResponse('error', '"' . $category->name . '" is a system category and cannot be deleted.');
        }

        // Block if category is used by any expense
        if ($category->expenses()->count() > 0) {
            return $this->ajaxResponse('error', 'This category is assigned to one or more expenses and cannot be deleted.');
        }

        $category->delete();

        return $this->ajaxResponse('success', 'Category deleted successfully.');
    }
}

