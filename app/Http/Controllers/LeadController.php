<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Lead;
use App\Services\LeadService;
use Yajra\DataTables\Facades\DataTables;

class LeadController extends Controller
{
    protected LeadService $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    public function index()
    {
        return view('pages.leads.index');
    }

    public function show($id)
    {
        $orgId = auth()->user()->organisation_id;
        $lead = Lead::where('organisation_id', $orgId)
            ->with(['measurements', 'measurements.items', 'quotations', 'quotations.items', 'followups', 'customer', 'project', 'project.createdBy'])
            ->findOrFail($id);
        
        return view('pages.leads.show', compact('lead'));
    }

    public function getLeadsData()
    {
        $query = Lead::query();

        return DataTables::of($query)
            ->addColumn('contact', function ($lead) {
                $url = route('leads.show', $lead->id);
                return '<a href="' . $url . '" class="fw-bold text-dark text-decoration-none">' . $lead->contact_name . '</a><br><small>' . $lead->contact_mobile . '</small>';
            })
            ->editColumn('source', function ($lead) {
                return '<span class="badge bg-secondary">' . ucfirst(str_replace('_', ' ', $lead->source)) . '</span>';
            })
            ->editColumn('status', function ($lead) {
                $class = [
                    'new' => 'bg-info',
                    'contacted' => 'bg-primary',
                    'site_visit_scheduled' => 'bg-warning text-dark',
                    'measurement_pending' => 'bg-warning',
                    'measurement_completed' => 'bg-success',
                    'quotation_sent' => 'bg-info',
                    'negotiation' => 'bg-primary',
                    'confirmed' => 'bg-success',
                    'lost' => 'bg-danger'
                ][$lead->status] ?? 'bg-secondary';
                return '<span class="badge ' . $class . '">' . ucfirst(str_replace('_', ' ', $lead->status)) . '</span>';
            })
            ->editColumn('created_at', function ($lead) {
                return $lead->created_at->format('d M Y');
            })
            ->addColumn('actions', function ($lead) {
                return '<button class="btn btn-sm btn-primary modal-trigger" data-url="' . route('leads.edit', $lead->id) . '" data-title="Edit Lead" data-size="modal-lg"><i class="fas fa-edit"></i> Edit</button>';
            })
            ->rawColumns(['contact', 'source', 'status', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $html = view('pages.leads.create')->render();
        
        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Add Lead'
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        $rules = [
            'contact_name' => 'required|string|max:255',
            'contact_mobile' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'source' => 'required|in:website,facebook,instagram,google,referral,builder,architect,walk_in,other',
            'lead_type' => 'required|in:residential,commercial',
            'project_type' => 'nullable|in:villa,apartment,office,shop,hotel'
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $validated = $validator->validated();
        
        $data = array_merge($validated, [
            'organisation_id' => $orgId,
            'alternate_mobile' => $request->alternate_mobile,
            'project_address' => $request->project_address,
            'expected_budget' => $request->expected_budget,
            'notes' => $request->notes,
            'status' => 'new',
        ]);
        
        $this->leadService->create($data);

        return $this->ajaxResponse('success', 'Lead created successfully!', [
            'close_modal' => true,
            'reload_table' => 'leads-table'
        ]);
    }

    public function checkMobile(\Illuminate\Http\Request $request)
    {
        $mobile = $request->mobile;
        $orgId = auth()->user()->organisation_id;

        // 1. Check existing leads
        $lead = Lead::where('organisation_id', $orgId)
                    ->where('contact_mobile', $mobile)
                    ->first();
        
        if ($lead) {
            return response()->json([
                'status' => 'exists',
                'lead_id' => $lead->id,
                'message' => 'An active lead already exists for this mobile number.'
            ]);
        }

        // 2. Check existing customers
        $customer = \App\Models\Customer::where('organisation_id', $orgId)
                        ->where('phone', $mobile)
                        ->first();

        if ($customer) {
            return response()->json([
                'status' => 'exists',
                'customer_id' => $customer->id,
                'message' => 'This mobile number is registered to an existing customer.'
            ]);
        }

        return response()->json(['status' => 'new']);
    }

    public function edit($id)
    {
        $orgId = auth()->user()->organisation_id;
        $lead = Lead::where('organisation_id', $orgId)->findOrFail($id);
        
        $html = view('pages.leads.edit', compact('lead'))->render();
        
        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Edit Lead'
        ]);
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $orgId = auth()->user()->organisation_id;
        $lead = Lead::where('organisation_id', $orgId)->findOrFail($id);

        $rules = [
            'contact_name' => 'required|string|max:255',
            'contact_mobile' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'source' => 'required|in:website,facebook,instagram,google,referral,builder,architect,walk_in,other',
            'lead_type' => 'required|in:residential,commercial',
            'project_type' => 'nullable|in:villa,apartment,office,shop,hotel',
            'status' => 'required|in:new,contacted,site_visit_scheduled,measurement_pending,measurement_completed,quotation_sent,negotiation,lost,confirmed',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $validated = $validator->validated();
        
        $data = array_merge($validated, [
            'alternate_mobile' => $request->alternate_mobile,
            'project_address' => $request->project_address,
            'expected_budget' => $request->expected_budget,
            'notes' => $request->notes,
        ]);
        
        $this->leadService->update($id, $data);

        return $this->ajaxResponse('success', 'Lead updated successfully!', [
            'close_modal' => true,
            'reload_table' => 'leads-table'
        ]);
    }
}
