<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BillingEngineService;

class ProcessBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes the background billing engine strictly progressing chronologically over module evaluation, invoicing, cancellations, and finally executing aged blackouts.';

    /**
     * Execute the console command.
     */
    public function handle(BillingEngineService $billingEngineService)
    {
        $this->info("Initializing BuildDesk Billing Process Orchestrator...");

        // Fire standalone logical sequential evaluations completely stripped
        // of native explicit physical conditional bounds natively inside CLI loop
        $billingEngineService->process();

        $this->info("Billing processes structurally finalized natively without operational errors.");
    }
}
