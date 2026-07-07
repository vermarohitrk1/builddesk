<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\OrganisationRepository;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class OrganisationController extends Controller
{
    protected OrganisationRepository $organisationRepository;

    public function __construct(OrganisationRepository $organisationRepository)
    {
        $this->organisationRepository = $organisationRepository;
    }

    public function index()
    {
        return view('pages.admin.organisations.index');
    }

    public function getOrganisationsData()
    {
        $query = \App\Models\Organisation::query();

        return DataTables::of($query)
            ->addColumn('logo', function ($org) {
                if ($org->logo) {
                    return '<img src="' . asset('storage/' . $org->logo) . '" alt="Logo" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">';
                }
                return '<div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary fw-bold" style="width: 40px; height: 40px;">' . substr($org->name, 0, 1) . '</div>';
            })
            ->editColumn('name', function ($org) {
                return '<div class="fw-bold text-dark">' . $org->name . '</div><small class="text-muted">' . ($org->email ?? 'No email') . '</small>';
            })
            ->editColumn('created_at', function ($org) {
                return $org->created_at ? $org->created_at->format('M d, Y') : '-';
            })
            ->editColumn('active_status', function ($org) {
                $badges = [
                    'active' => 'bg-success',
                    'suspended' => 'bg-danger',
                    'trial' => 'bg-warning text-dark',
                    'inactive' => 'bg-secondary',
                    'expired' => 'bg-dark'
                ];
                $class = $badges[$org->active_status] ?? 'bg-secondary';
                return '<span class="badge ' . $class . '">' . ucfirst($org->active_status) . '</span>';
            })
            ->addColumn('actions', function ($org) {
                $btns = '<button class="btn btn-sm btn-primary modal-trigger" data-url="' . route('admin.organisations.edit', $org->id) . '" data-title="Edit Organisation" data-size="modal-lg"><i class="fas fa-edit"></i> Edit</button>';
                if (Auth::guard('super_admin')->check()) {
                    $btns .= ' <a href="' . route('admin.impersonate', $org->id) . '" class="btn btn-sm btn-warning"><i class="fas fa-user-secret"></i> Login</a>';
                }
                return $btns;
            })
            ->rawColumns(['logo', 'name', 'active_status', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $html = view('pages.admin.organisations.create')->render();
        return response()->json(['status' => 'success', 'data' => ['html' => $html, 'title' => 'Add Organisation']]);
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:50',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'active_status' => 'required|in:active,inactive,suspended,trial,expired',
            'payment_status' => 'nullable|in:paid,overdue',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $data = $validator->validated();
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('organisations', 'public');
        }

        $this->organisationRepository->create($data);

        return $this->ajaxResponse('success', 'Organisation created successfully!', []);
    }

    public function edit($id)
    {
        $organisation = $this->organisationRepository->getById($id);
        $html = view('pages.admin.organisations.edit', compact('organisation'))->render();
        return response()->json(['status' => 'success', 'data' => ['html' => $html, 'title' => 'Edit Organisation']]);
    }

    public function update(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:50',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'active_status' => 'required|in:active,inactive,suspended,trial,expired',
            'payment_status' => 'nullable|in:paid,overdue',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $data = $validator->validated();
        
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('organisations', 'public');
        }

        $this->organisationRepository->update($id, $data);

        return $this->ajaxResponse('success', 'Organisation updated successfully!', []);
    }
}
