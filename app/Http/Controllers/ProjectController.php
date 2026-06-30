<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{

    public function index()
    {
        $orgId = auth()->user()->organisation_id;

        $projects = Project::where('organisation_id', $orgId)->get();

        return view('pages.projects.index', compact('projects'));
    }

    public function show($id)
    {
        $orgId = auth()->user()->organisation_id;

        $project = Project::where('organisation_id', $orgId)->findOrFail($id);

        $lead = Lead::where('organisation_id', $orgId)
            ->with(['measurements', 'measurements.items', 'quotations', 'quotations.items', 'followups', 'customer', 'project', 'project.createdBy'])
            ->findOrFail($project->lead_id);
        
        return view('pages.leads.show', compact('lead'));
    }

    public function store(Request $request)
    {
        $orgId = Auth::user()->organisation_id;

        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $lead = Lead::where('organisation_id', $orgId)->findOrFail($request->lead_id);

        // Guard: one project per lead
        if ($lead->project) {
            return $this->ajaxResponse('error', 'A project already exists for this lead.');
        }

        // Auto-generate project number: PRJ-YYYYMM-XXXX
        $lastProject = Project::where('organisation_id', $orgId)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastProject ? (intval(substr($lastProject->project_number, -4)) + 1) : 1;
        $projectNumber = 'PRJ-' . now()->format('Ym') . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        Project::create([
            'organisation_id' => $orgId,
            'lead_id' => $lead->id,
            'project_number' => $projectNumber,
            'started_at' => now(),
            'created_by' => Auth::id(),
        ]);

        // Update lead status to confirmed
        $lead->update(['status' => 'confirmed']);

        return $this->ajaxResponse('success', 'Project started successfully!', [
            'reload_page' => true,
        ]);
    }

    public function destroy($id)
    {
        $orgId = Auth::user()->organisation_id;
        $project = Project::where('organisation_id', $orgId)->findOrFail($id);
        $lead = $project->lead;

        $project->delete();

        // Reset lead status to negotiation
        $lead->update(['status' => 'negotiation']);

        return $this->ajaxResponse('success', 'Project removed.', [
            'reload_page' => true,
        ]);
    }
}
