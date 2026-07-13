<?php

namespace App\Services;

use App\Models\OrganisationSubscription;

class SuspensionService
{
    /**
     * Checks all pending invoices across active SLAs and enforces account
     * suspensions if the due_date inherently exceeding grace periods.
     */
    public function checkSuspension()
    {
        // Explicitly isolate Active subscriptions (Ignore Trial, Cancelled, and pre-Suspended)
        $subscriptions = OrganisationSubscription::where('status', 'Active')->get();

        foreach ($subscriptions as $sub) {
            // Find if there is any pending invoice mathematically breached past runtime
            // (Due date inherits 7-day grace natively from InvoiceService generation logic)
            $hasOverdue = $sub->invoices()
                ->where('status', 'Pending')
                ->where('due_date', '<', now())
                ->exists();

            if ($hasOverdue) {
                // Execute standard blackout protocol updating status mapping
                $sub->update([
                    'status' => 'Suspended',
                    'remarks' => 'Subscription suspended automatically due to overdue invoice.'
                ]);
            }
        }
    }
}
