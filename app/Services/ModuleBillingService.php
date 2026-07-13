<?php

namespace App\Services;

use App\Models\OrganisationModuleUsageHistory;
use App\Models\OrganisationSubscription;
use App\Models\OrganisationModule;

class ModuleBillingService
{
    /**
     * Executes healing protocols prior to evaluating limits.
     * Parses all OrganisationModuleUsageHistory blocks actively enabled natively
     * to identify modules exceeding 24-hour evaluation benchmarks and flags them as natively `is_chargeable`.
     */
    public function checkModuleEvaluations()
    {
        $this->selfHealUsageHistories();

        // Target active segments (disabled_at IS NULL) that are evaluated as free (is_chargeable = false)
        // explicitly ensuring they've elapsed strictly their 24 hour buffer natively.
        $eligibleHistories = OrganisationModuleUsageHistory::where('is_chargeable', false)
            ->whereNull('disabled_at')
            ->where('enabled_at', '<=', now()->subHours(24))
            ->get();

        foreach ($eligibleHistories as $history) {
            $history->update(['is_chargeable' => true]);
        }
    }

    /**
     * Traverses enabled modules repairing broken or missing history tracks.
     */
    protected function selfHealUsageHistories()
    {
        $subscriptions = OrganisationSubscription::whereIn('status', ['Active', 'Trial'])->get();

        foreach ($subscriptions as $sub) {
            $enabledModules = OrganisationModule::join('modules', 'modules.id', '=', 'organisation_modules.module_id')
                ->where('organisation_modules.organisation_id', $sub->organisation_id)
                ->where('organisation_modules.is_enabled', true)
                ->where('modules.is_core', false)
                ->select('organisation_modules.*')
                ->get();

            foreach ($enabledModules as $orgModule) {
                $hasActiveHistory = OrganisationModuleUsageHistory::where('organisation_id', $sub->organisation_id)
                    ->where('subscription_id', $sub->id)
                    ->where('module_id', $orgModule->module_id)
                    ->whereNull('disabled_at')
                    ->exists();

                if (!$hasActiveHistory) {
                    $isChargeable = $this->isChargeableImmediately($sub, $orgModule->module_id);

                    OrganisationModuleUsageHistory::create([
                        'organisation_id' => $sub->organisation_id,
                        'module_id' => $orgModule->module_id,
                        'subscription_id' => $sub->id,
                        'enabled_at' => now(),
                        'is_chargeable' => $isChargeable
                    ]);
                }
            }
        }
    }

    /**
     * Determines whether a newly toggled module receives a 24-hour evaluation or must 

     * be flagged chargeable instantly because the trial was natively already consumed 
     * within this active cycle mapping boundary.
     */
    public function isChargeableImmediately(OrganisationSubscription $subscription, $moduleId)
    {
        // Determine analytical exact anchor mathematically referencing current billing block start natively
        $cycleStart = $subscription->getCurrentCycleStart();

        // Analyze if ANY history slice exists natively mapped traversing past this cycle anchor for this explicit module physically
        $priorEvaluationExists = OrganisationModuleUsageHistory::where('subscription_id', $subscription->id)
            ->where('module_id', $moduleId)
            ->where('enabled_at', '>=', $cycleStart)
            ->exists();

        // If a prior historical line exists physically, the trial natively consumed itself.
        return $priorEvaluationExists;
    }
}
