<?php

namespace App\Services;

class BillingEngineService
{
    protected $invoiceService;
    protected $suspensionService;
    protected $moduleBillingService;
    protected $subscriptionService;

    public function __construct(
        InvoiceService $invoiceService,
        SuspensionService $suspensionService,
        ModuleBillingService $moduleBillingService,
        SubscriptionService $subscriptionService
    ) {
        $this->invoiceService = $invoiceService;
        $this->suspensionService = $suspensionService;
        $this->moduleBillingService = $moduleBillingService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Central orchestrator loop for executing standalone logic processors mapping
     * the BuildDesk manual billing lifecycle.
     */
    public function process()
    {
        // 1. Evaluate all overlapping Module trials mapping usage variables natively bounding against charging thresholds
        $this->moduleBillingService->checkModuleEvaluations();

        // 2. Monitor standard cyclic lifespans and construct newly spawned due Invoices isolating snapshot arrays
        $this->invoiceService->checkInvoiceGeneration();

        // 3. Process explicit Subscription termination events dynamically triggering explicit end-state invoice slicing
        $this->subscriptionService->checkCancelledSubscriptions();

        // 4. Force blackout suspensions targeting permanently aged invoices natively strictly tracking explicitly beyond limit thresholds
        $this->suspensionService->checkSuspension();
    }
}
