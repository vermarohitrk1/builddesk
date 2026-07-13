<?php

namespace App\Services;

use App\Models\OrganisationSubscription;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Identifies subscriptions that have completed a billing cycle and generates 
     * invoices for cycles missing an overlapping record.
     */
    public function checkInvoiceGeneration()
    {
        // Only target active or valid trial lifecycles for normal generation
        $subscriptions = OrganisationSubscription::whereIn('status', ['Active'])->get();

        foreach ($subscriptions as $sub) {
            $cycleStart = $sub->getCurrentCycleStart();
            
            // Traverse exactly one cycle backward to find the recently finished window
            $previousCycleStart = $cycleStart->copy()->subMonth();
            $previousCycleEnd = $cycleStart->copy()->subDay()->endOfDay();

            // Ensure we never backtrack beyond their original registration SLA parameters
            if ($previousCycleStart >= $sub->start_date->startOfDay()) {
                
                $invoiceExists = $sub->invoices()
                    ->whereDate('billing_start_date', $previousCycleStart)
                    ->exists();

                // If that explicit cycle just finished and wasn't invoiced, invoke generator.
                if (!$invoiceExists && $previousCycleEnd->isPast()) {
                    $this->createInvoiceForCycle($sub, $previousCycleStart, $previousCycleEnd);
                }
            }
        }
    }

    /**
     * Snapshots prices and constructs the final relational invoice payload.
     */
    public function createInvoiceForCycle($subscription, $startDate, $endDate)
    {
        DB::transaction(function () use ($subscription, $startDate, $endDate) {
            
            // Standard formatting token avoiding collisions
            $invoiceNumber = 'INV-' . strtoupper(Str::random(8));

            // Pull ALL usage blocks overlapping the cycle that exhausted evaluation limits
            $usageHistories = $subscription->usageHistories()
                ->where('is_chargeable', true)
                ->where('enabled_at', '<=', $endDate)
                ->where(function ($query) use ($startDate) {
                    $query->whereNull('disabled_at')
                          ->orWhere('disabled_at', '>=', $startDate);
                })
                ->with('module')
                ->get();

            // Build structural anchor
            $invoice = Invoice::create([
                'organisation_id' => $subscription->organisation_id,
                'subscription_id' => $subscription->id,
                'invoice_number' => $invoiceNumber,
                'billing_start_date' => $startDate,
                'billing_end_date' => $endDate,
                // Zeroed prior to calculation
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'status' => 'Pending',
                'due_date' => $endDate->copy()->addDays(7),
                'generated_at' => now(),
            ]);

            $subtotal = 0;

            // Generate BuildDesk Core Package explicitly
            $coreModulesSum = \App\Models\Module::where('is_core', true)->sum('price');
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'module_id' => null,
                'title' => 'BuildDesk Core Package',
                'description' => 'Base subscription package including core modules',
                'quantity' => 1,
                'unit_price' => $coreModulesSum,
                'total_price' => $coreModulesSum,
            ]);

            $subtotal += $coreModulesSum;

            // Ensure we don't bill identical modules twice if they toggled it multiple times in one month
            $uniqueUsages = $usageHistories->unique('module_id');

            foreach ($uniqueUsages as $history) {
                $module = $history->module;
                if (!$module) continue;

                $price = $module->price; // Snapshot price

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'module_id' => $module->id,
                    'title' => $module->name,
                    'description' => "Billing cycle for module {$module->name}",
                    'quantity' => 1,
                    'unit_price' => $price,
                    'total_price' => $price,
                ]);

                $subtotal += $price;
            }

            // Seal aggregated totals permanently
            $invoice->update([
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
            ]);
        });
    }
}
