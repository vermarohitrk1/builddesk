<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadFollowup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeadFollowupController extends Controller
{
    public function create(Request $request)
    {
        $leadId = $request->lead_id;
        $orgId = Auth::user()->organisation_id;
        $lead = Lead::where('organisation_id', $orgId)->findOrFail($leadId);

        $html = view('pages.followups.create', compact('lead'))->render();

        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Add Follow-up'
        ]);
    }

    public function store(Request $request)
    {
        $orgId = Auth::user()->organisation_id;

        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'followup_date' => 'required|date',
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $lead = Lead::where('organisation_id', $orgId)->findOrFail($request->lead_id);

        LeadFollowup::create([
            'organisation_id' => $orgId,
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'followup_date' => $request->followup_date,
            'note' => $request->note,
        ]);

        $activeTab = 'followups';

        return $this->ajaxResponse('success', 'Follow-up added successfully!', [
            'close_modal' => true,
            'update' => [
                '#leadTabsContent' => [
                    'action' => 'replace',
                    'html' => view('pages.leads.components.tabs', compact('lead', 'activeTab'))->render()
                ]
            ]
        ]);
    }

    public function edit($id)
    {
        $orgId = Auth::user()->organisation_id;
        $followup = LeadFollowup::where('organisation_id', $orgId)->findOrFail($id);
        $lead = $followup->lead;

        $html = view('pages.followups.edit', compact('followup', 'lead'))->render();

        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Edit Follow-up'
        ]);
    }

    public function update(Request $request, $id)
    {
        $orgId = Auth::user()->organisation_id;
        $followup = LeadFollowup::where('organisation_id', $orgId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'followup_date' => 'required|date',
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($validator);
        }

        $followup->update([
            'followup_date' => $request->followup_date,
            'note' => $request->note,
        ]);

        $lead = $followup->lead;
        $activeTab = 'followups';

        return $this->ajaxResponse('success', 'Follow-up updated successfully!', [
            'close_modal' => true,
            'update' => [
                '#leadTabsContent' => [
                    'action' => 'replace',
                    'html' => view('pages.leads.components.tabs', compact('lead', 'activeTab'))->render()
                ]
            ]
        ]);
    }

    public function complete($id)
    {
        $orgId = Auth::user()->organisation_id;
        $followup = LeadFollowup::where('organisation_id', $orgId)->findOrFail($id);

        $followup->update([
            'completed_at' => now(),
        ]);

        $lead = $followup->lead;
        $activeTab = 'followups';

        return $this->ajaxResponse('success', 'Follow-up marked as completed!', [
            'update' => [
                '#leadTabsContent' => [
                    'action' => 'replace',
                    'html' => view('pages.leads.components.tabs', compact('lead', 'activeTab'))->render()
                ]
            ]
        ]);
    }

    public function destroy($id)
    {
        $orgId = Auth::user()->organisation_id;
        $followup = LeadFollowup::where('organisation_id', $orgId)->findOrFail($id);
        $lead = $followup->lead;

        $followup->delete();

        $activeTab = 'followups';

        return $this->ajaxResponse('success', 'Follow-up deleted successfully!', [
            'update' => [
                '#leadTabsContent' => [
                    'action' => 'replace',
                    'html' => view('pages.leads.components.tabs', compact('lead', 'activeTab'))->render()
                ]
            ]
        ]);
    }
}
