<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\OrganisationService;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class OrganisationController extends Controller
{
    protected OrganisationService $organisationService;

    public function __construct(OrganisationService $organisationService)
    {
        $this->organisationService = $organisationService;
    }

    public function index()
    {
        return view('pages.organisations.index');
    }

    public function getOrganisationsData()
    {
        $query = \App\Models\Organisation::query();

        return DataTables::of($query)
            ->addColumn('plan', function ($org) {
                return $org->subscriptionPlan->name ?? 'None';
            })
            ->editColumn('active_status', function ($org) {
                $class = $org->active_status === 'active' ? 'bg-success' : 'bg-danger';
                return '<span class="badge ' . $class . '">' . ucfirst($org->active_status) . '</span>';
            })
            ->addColumn('actions', function ($org) {
                $btns = '<button class="btn btn-sm btn-primary modal-trigger" data-url="' . route('organisations.edit', $org->id) . '" data-title="Edit Organisation"><i class="fas fa-edit"></i> Edit</button>';
                $btns .= ' <a href="' . route('tenant.impersonate', $org->id) . '" class="btn btn-sm btn-warning"><i class="fas fa-user-secret"></i> Login</a>';
                return $btns;
            })
            ->rawColumns(['active_status', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $html = view('pages.organisations.create')->render();
        return response()->json(['status' => 'success', 'data' => ['html' => $html, 'title' => 'Add Organisation']]);
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'active_status' => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $this->organisationService->create($validator->validated());

        return $this->ajaxResponse('success', 'Organisation created successfully!', []);
    }

    public function edit($id)
    {
        $organisation = $this->organisationService->getById($id);
        $html = view('pages.organisations.edit', compact('organisation'))->render();
        return response()->json(['status' => 'success', 'data' => ['html' => $html, 'title' => 'Edit Organisation']]);
    }

    public function update(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'active_status' => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $this->organisationService->update($id, $validator->validated());

        return $this->ajaxResponse('success', 'Organisation updated successfully!', []);
    }
}
