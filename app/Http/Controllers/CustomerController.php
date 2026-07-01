<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        return view('pages.customers.index');
    }

    public function getCustomersData()
    {
        $orgId = auth()->user()->organisation_id;
        $query = Customer::where('organisation_id', $orgId);

        return DataTables::of($query)
            ->addColumn('actions', function ($customer) {
                $editBtn = '<button class="btn btn-sm btn-link text-primary p-0 me-2 modal-trigger"
                    data-url="' . route('customers.edit', $customer->id) . '"
                    data-title="Edit Customer"
                    data-size="modal-md">
                    <i class="fas fa-edit"></i>
                </button>';

                $deleteBtn = '<a href="' . route('customers.destroy', $customer->id) . '"
                    class="btn btn-sm btn-link text-danger p-0 ajax-link"
                    data-method="DELETE"
                    data-confirm="Are you sure you want to delete this customer?">
                    <i class="fas fa-trash"></i>
                </a>';

                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        $html = view('pages.customers.create')->render();

        return $this->ajaxResponse('success', '', [
            'html'  => $html,
            'title' => 'Add Customer',
        ]);
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        Customer::create([
            'organisation_id' => $orgId,
            'name'            => $request->name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'address'         => $request->address,
        ]);

        return $this->ajaxResponse('success', 'Customer created successfully!', [
            'close_modal'  => true,
            'reload_table' => '#customers-table',
        ]);
    }

    public function edit($id)
    {
        $orgId    = auth()->user()->organisation_id;
        $customer = Customer::where('organisation_id', $orgId)->findOrFail($id);

        $html = view('pages.customers.edit', compact('customer'))->render();

        return $this->ajaxResponse('success', '', [
            'html'  => $html,
            'title' => 'Edit Customer',
        ]);
    }

    public function update(Request $request, $id)
    {
        $orgId    = auth()->user()->organisation_id;
        $customer = Customer::where('organisation_id', $orgId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $customer->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);

        return $this->ajaxResponse('success', 'Customer updated successfully!', [
            'close_modal'  => true,
            'reload_table' => '#customers-table',
        ]);
    }

    public function destroy($id)
    {
        $orgId    = auth()->user()->organisation_id;
        $customer = Customer::where('organisation_id', $orgId)->findOrFail($id);

        // Block deletion if customer has any lead linked to a project
        $hasProject = Lead::where('customer_id', $id)
            ->where('organisation_id', $orgId)
            ->has('project')
            ->exists();

        // Also block if any lead is in 'confirmed' status
        $hasConfirmedLead = Lead::where('customer_id', $id)
            ->where('organisation_id', $orgId)
            ->where('status', 'confirmed')
            ->exists();

        if ($hasProject || $hasConfirmedLead) {
            return $this->ajaxResponse('error', 'Cannot delete this customer — they have an active or completed project.');
        }

        $customer->delete();

        return $this->ajaxResponse('success', 'Customer deleted successfully!', [
            'reload_table' => '#customers-table',
        ]);
    }
}
