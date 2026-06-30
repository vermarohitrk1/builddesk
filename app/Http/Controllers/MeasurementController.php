<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Measurement;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function create(Request $request)
    {
        $leadId = $request->lead_id;
        $orgId = auth()->user()->organisation_id;
        $lead = Lead::where('organisation_id', $orgId)->findOrFail($leadId);

        $html = view('pages.measurements.create', compact('lead'))->render();

        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Add Measurement'
        ]);
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        
        $validated = \Validator::make($request->all(),[
            'lead_id' => 'required|exists:leads,id',
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);
        
        if ($validated->fails()) {
            return $this->validationResponse($validated);
        }

        $lead = Lead::where('organisation_id', $orgId)->findOrFail($request->lead_id);
        
        \DB::transaction(function () use ($orgId, $lead, $request) {
            $measurement = Measurement::create([
                'organisation_id' => $orgId,
                'lead_id' => $lead->id,
                'customer_id' => $lead->customer_id,
                'title' => $request->title,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $key => $item) {
                $measurement->items()->create([
                    'title' => $request->item_title[$key],
                    'description' => $request->item_description[$key],
                ]);
            }
        });

        return $this->ajaxResponse('success', 'Measurement recorded successfully!', [
            'close_modal' => true,
            'reload_table' => 'leads-table',
            'update' => ['#leadTabsContent' => ['action' => 'replace', 'html' => view('pages.leads.components.tabs', compact('lead'))->render()]]
        ]);
    }

    public function edit($id)
    {
        $orgId = auth()->user()->organisation_id;
        $measurement = Measurement::where('organisation_id', $orgId)->with('items')->findOrFail($id);
        $lead = $measurement->lead;

        $html = view('pages.measurements.edit', compact('measurement', 'lead'))->render();

        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Edit Measurement'
        ]);
    }

    public function update(Request $request, $id)
    {
        $orgId = auth()->user()->organisation_id;
        $measurement = Measurement::where('organisation_id', $orgId)->findOrFail($id);
        
        $validated = \Validator::make($request->all(),[
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);
        
        if ($validated->fails()) {
            return $this->validationResponse($validated);
        }

        $lead = $measurement->lead;
        
        \DB::transaction(function () use ($measurement, $request) {
            $measurement->update([
                'title' => $request->title,
                'notes' => $request->notes,
            ]);

            // Simple update: delete existing items and recreate
            $measurement->items()->delete();
            
            if ($request->items) {
                foreach ($request->items as $key => $item) {
                    $measurement->items()->create([
                        'title' => $request->item_title[$key],
                        'description' => $request->item_description[$key],
                    ]);
                }
            }
        });

        return $this->ajaxResponse('success', 'Measurement updated successfully!', [
            'close_modal' => true,
            'update' => ['#leadTabsContent' => ['action' => 'replace', 'html' => view('pages.leads.components.tabs', compact('lead'))->render()]]
        ]);
    }

    public function destroy($id)
    {
        $orgId = auth()->user()->organisation_id;
        $measurement = Measurement::where('organisation_id', $orgId)->findOrFail($id);
        $lead = $measurement->lead;

        \DB::transaction(function () use ($measurement) {
            $measurement->items()->delete();
            $measurement->delete();
        });

        return $this->ajaxResponse('success', 'Measurement deleted successfully!', [
            'update' => ['#leadTabsContent' => ['action' => 'replace', 'html' => view('pages.leads.components.tabs', compact('lead'))->render()]]
        ]);
    }

    public function addItemRow(Request $request)
    {
        $html = view('pages.measurements.components.item')->render();
        return $this->ajaxResponse('success', '', ['html' => $html]);
    }
}
