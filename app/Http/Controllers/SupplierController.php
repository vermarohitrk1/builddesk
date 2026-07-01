<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function getData()
    {
        $orgId = Auth::user()->organisation_id;
        $query = Supplier::where('organisation_id', $orgId)->select(['id', 'name', 'mobile', 'gst_number', 'created_at']);

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                $editBtn = '<button class="btn btn-sm btn-link p-0 text-primary modal-trigger" data-url="' . route('suppliers.edit', $row->id) . '" data-title="Edit Supplier"><i class="fas fa-edit"></i></button>';
                $deleteBtn = '<a href="' . route('suppliers.destroy', $row->id) . '"
                    class="btn btn-sm btn-link text-danger p-0 ajax-link"
                    data-method="DELETE"
                    data-confirm="Are you sure you want to delete this supplier? This cannot be undone.">
                    <i class="fas fa-trash"></i>
                </a>';
                return $editBtn . ' ' . $deleteBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        $html = view('pages.expenses.suppliers.create')->render();
        return $this->ajaxResponse('success', '', ['html' => $html]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'mobile'     => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:50',
            'address'    => 'nullable|string',
            'notes'      => 'nullable|string',
        ]);

        $orgId = Auth::user()->organisation_id;

        $supplier = Supplier::create([
            'organisation_id' => $orgId,
            'name'            => $request->name,
            'mobile'          => $request->mobile,
            'gst_number'      => $request->gst_number,
            'address'         => $request->address,
            'notes'           => $request->notes,
        ]);

        return $this->ajaxResponse('success', 'Supplier created successfully!', [
            'supplier'     => $supplier,
            'close_modal'  => true,
            'reload_table' => 'suppliers-table',
        ]);
    }

    public function edit($id)
    {
        $orgId    = Auth::user()->organisation_id;
        $supplier = Supplier::where('organisation_id', $orgId)->findOrFail($id);
        $html     = view('pages.expenses.suppliers.edit', compact('supplier'))->render();
        return $this->ajaxResponse('success', '', ['html' => $html]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'mobile'     => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:50',
            'address'    => 'nullable|string',
            'notes'      => 'nullable|string',
        ]);

        $orgId    = Auth::user()->organisation_id;
        $supplier = Supplier::where('organisation_id', $orgId)->findOrFail($id);

        $supplier->update($request->only(['name', 'mobile', 'gst_number', 'address', 'notes']));

        return $this->ajaxResponse('success', 'Supplier updated successfully!', [
            'close_modal'  => true,
            'reload_table' => 'suppliers-table',
        ]);
    }

    public function destroy($id)
    {
        $orgId    = Auth::user()->organisation_id;
        $supplier = Supplier::where('organisation_id', $orgId)->findOrFail($id);

        if ($supplier->expenses()->count() > 0) {
            return $this->ajaxResponse('error', 'This supplier is linked to one or more expenses and cannot be deleted.');
        }

        $supplier->delete();

        return $this->ajaxResponse('success', 'Supplier deleted successfully.', [
            'reload_table' => 'suppliers-table',
        ]);
    }
}
