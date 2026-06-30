<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Organisation;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function create(Request $request)
    {
        $leadId = $request->lead_id;
        $orgId = auth()->user()->organisation_id;
        $lead = Lead::where('organisation_id', $orgId)->findOrFail($leadId);

        $html = view('pages.quotations.create', compact('lead'))->render();

        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Create Quotation'
        ]);
    }

    public function store(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        
        $validated = \Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'quotation_number' => 'required|string|unique:quotations,quotation_number',
            'total_amount' => 'required|numeric|min:0',
            'terms' => 'nullable|string',
        ]);

        if ($validated->fails()) {
            return $this->validationResponse($validated);
        }

        $lead = Lead::where('organisation_id', $orgId)->findOrFail($request->lead_id);

        \DB::transaction(function () use ($orgId, $lead, $request) {

            $sub_total = 0;
            $discount_value = 0;
            $discount_amount = 0;
            $total_amount = 0;

            if ($request->items) {
                foreach ($request->items as $key => $item) {
                    $sub_total += $request->item_amount[$key];
                }
            }

            if ($request->discount_type == 'percentage') {
                $discount_value = $request->discount_value;
                $discount_amount = $sub_total * ($discount_value / 100);
            } else {
                $discount_amount = $discount_value = $request->discount_value;
            }

            $total_amount = $sub_total - $discount_amount;

            $quotation = Quotation::create([
                'organisation_id' => $orgId,
                'lead_id' => $lead->id,
                'customer_id' => $lead->customer_id,
                'quotation_number' => $request->quotation_number,
                'sub_total' => $sub_total,
                'discount_type' => $request->discount_type,
                'discount_value' => $discount_value,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
                'terms' => $request->terms,
                'status' => 'draft',
            ]);

            foreach ($request->items as $key => $item) {
                $quotation->items()->create([
                    'description' => $request->item_description[$key],
                    'amount' => $request->item_amount[$key],
                ]);
            }
        });

        return $this->ajaxResponse('success', 'Quotation created successfully!', [
            'close_modal' => true,
            'update' => ['#leadTabsContent' => ['action' => 'replace', 'html' => view('pages.leads.components.tabs', compact('lead'))->render()]]
        ]);
    }

    public function edit($id)
    {
        $orgId = auth()->user()->organisation_id;
        $quotation = Quotation::where('organisation_id', $orgId)->with('items')->findOrFail($id);
        $lead = $quotation->lead;

        $html = view('pages.quotations.edit', compact('quotation', 'lead'))->render();

        return $this->ajaxResponse('success', '', [
            'html' => $html,
            'title' => 'Edit Quotation'
        ]);
    }

    public function update(Request $request, $id)
    {
        $orgId = auth()->user()->organisation_id;
        $quotation = Quotation::where('organisation_id', $orgId)->findOrFail($id);
        
        $validated = \Validator::make($request->all(), [
            'quotation_number' => 'required|string|unique:quotations,quotation_number,' . $id,
            'total_amount' => 'required|numeric|min:0',
            'terms' => 'nullable|string',
        ]);

        if ($validated->fails()) {
            return $this->validationResponse($validated);
        }

        $lead = $quotation->lead;

        \DB::transaction(function () use ($quotation, $request) {

            $sub_total = 0;
            $discount_value = 0;
            $discount_amount = 0;
            $total_amount = 0;

            if ($request->items) {
                foreach ($request->items as $key => $item) {
                    $quotation->items()->updateOrCreate([
                        'id' => $item,
                    ], [
                        'description' => $request->item_description[$key],
                        'amount' => $request->item_amount[$key],
                    ]);

                    $sub_total += $request->item_amount[$key];
                }
            }

            if ($request->discount_type == 'percentage') {
                $discount_value = $request->discount_value;
                $discount_amount = $sub_total * ($discount_value / 100);
            } else {
                $discount_amount = $discount_value = $request->discount_value;
            }

            $total_amount = $sub_total - $discount_amount;


            $quotation->update([
                'quotation_number' => $request->quotation_number,
                'sub_total' => $sub_total,
                'discount_type' => $request->discount_type,
                'discount_value' => $discount_value,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
                'terms' => $request->terms,
            ]);
        });

        $activeTab = 'quotations';

        return $this->ajaxResponse('success', 'Quotation updated successfully!', [
            'close_modal' => true,
            'update' => ['#leadTabsContent' => ['action' => 'replace', 'html' => view('pages.leads.components.tabs', compact('lead', 'activeTab'))->render()]]
        ]);
    }

    public function destroy($id)
    {
        $orgId = auth()->user()->organisation_id;
        $quotation = Quotation::where('organisation_id', $orgId)->findOrFail($id);
        $lead = $quotation->lead;

        \DB::transaction(function () use ($quotation) {
            $quotation->items()->delete();
            $quotation->delete();
        });

        return $this->ajaxResponse('success', 'Quotation deleted successfully!', [
            'update' => ['#leadTabsContent' => ['action' => 'replace', 'html' => view('pages.leads.components.tabs', compact('lead'))->render()]]
        ]);
    }

    public function getMeasurementSuggestions(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        // Accept lead_id from query param or fall back to the latest lead
        $leadId = $request->lead_id;
        
        if ($leadId) {
            $lead = Lead::where('organisation_id', $orgId)->findOrFail($leadId);
        } else {
            $lead = Lead::where('organisation_id', $orgId)->latest()->first();
        }

        if (!$lead) {
            return $this->ajaxResponse('success', '', ['items' => []]);
        }

        // Gather all measurement items across all measurements for this lead
        $items = $lead->measurements()
            ->with('items')
            ->get()
            ->flatMap(function ($measurement) {
                return $measurement->items->map(function ($item) use ($measurement) {
                    return [
                        'title' => $item->title,
                        'description' => $item->description,
                        'measurement_title' => $measurement->title,
                    ];
                });
            })
            ->values()
            ->all();

        return $this->ajaxResponse('success', '', ['items' => $items]);
    }

    public function downloadPdf($id)
    {
        $orgId = auth()->user()->organisation_id;
        $quotation = Quotation::where('organisation_id', $orgId)
            ->with('items')
            ->findOrFail($id);

        $organisation = Organisation::findOrFail($orgId);
        $customer     = $quotation->customer;

        $pdf = Pdf::loadView('pdf.quotation', compact('quotation', 'organisation', 'customer'))
            ->setPaper('a4', 'portrait');

        $filename = 'Quotation_' . $quotation->quotation_number . '.pdf';

        return $pdf->stream('Quotation_' . $quotation->quotation_number . '.pdf');
        // return $pdf->download($filename);
    }

    public function previewPdf($id)
    {
        $orgId = auth()->user()->organisation_id;
        $quotation = Quotation::where('organisation_id', $orgId)
            ->with('items')
            ->findOrFail($id);

        $organisation = Organisation::findOrFail($orgId);
        $customer     = $quotation->customer;

        $pdf = Pdf::loadView('pdf.quotation', compact('quotation', 'organisation', 'customer'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Quotation_' . $quotation->quotation_number . '.pdf');
    }

    public function addItemRow(Request $request)
    {
        $html = view('pages.quotations.components.item')->render();
        return $this->ajaxResponse('success', '', ['html' => $html]);
    }
}