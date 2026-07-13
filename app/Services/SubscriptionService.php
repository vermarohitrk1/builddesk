<?php

namespace App\Services;

use App\Models\OrganisationSubscription;

class SubscriptionService
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Secures explicit End-of-Life states for Cancelled subscriptions strictly after
     * their cycle math bounds expire, and delegates execution of their final billing block.
     */
    public function checkCancelledSubscriptions()
    {
        // Target explicitly cancelled organisations natively restricted strictly past their mathematically isolated EOL bounds
        $subscriptions = OrganisationSubscription::where('status', 'Cancelled')
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->get();

        foreach ($subscriptions as $sub) {
            $finalCycleEnd = $sub->end_date;
            
            // Mathematically invert the calculation relative strictly targeting the start boundary of that final month isolated block
            $finalCycleStart = $finalCycleEnd->copy()->addDay()->subMonth()->startOfDay();

            $finalInvoiceExists = $sub->invoices()
                ->whereDate('billing_end_date', $finalCycleEnd)
                ->exists();

            // Provided the execution bounds evaluate reliably and aren't preemptively traversing prior structurally to active sign-up boundaries
            if (!$finalInvoiceExists && $finalCycleStart >= $sub->start_date->startOfDay()) {
                
                // Instruct underlying snapshot orchestrator engine
                $this->invoiceService->createInvoiceForCycle($sub, $finalCycleStart, $finalCycleEnd);
            }
        }
    }
}
