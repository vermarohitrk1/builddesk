<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function store(Request $request)
    {
        $orgId = Auth::user()->organisation_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $supplier = Supplier::create([
            'organisation_id' => $orgId,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'gst_number' => $request->gst_number,
            'address' => $request->address,
            'notes' => $request->notes,
        ]);

        return $this->ajaxResponse('success', 'Supplier created successfully!', [
            'supplier' => $supplier
        ]);
    }
}
