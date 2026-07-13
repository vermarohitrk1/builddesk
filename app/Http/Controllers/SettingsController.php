<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        return view('pages.settings.index');
    }

    public function getBasic()
    {
        return view('pages.settings.partials.basic');
    }

    public function getExpenseCategories()
    {
        return view('pages.settings.partials.expense_categories');
    }

    public function getSuppliers()
    {
        return view('pages.settings.partials.suppliers');
    }

    public function getModules()
    {
        $availableModules = \App\Models\Module::orderBy('name')->get();
        $organisation = Auth::user()->organisation;

        // Ensure default records exist for this organisation
        foreach ($availableModules as $module) {
            $orgModule = \App\Models\OrganisationModule::firstOrCreate([
                'organisation_id' => $organisation->id,
                'module_id' => $module->id
            ]);

            if($module->is_core) {
                $orgModule->is_enabled = true;
                $orgModule->save();
            }
        }

        $availableModules = $availableModules->where('is_core', false);
        $currentSubscription = $organisation->currentSubscription;

        $organisationModules = \App\Models\OrganisationModule::where('organisation_id', $organisation->id)->get()->keyBy('module_id');
        
        $usageHistories = collect();
        if ($currentSubscription) {
            $usageHistories = \App\Models\OrganisationModuleUsageHistory::where('organisation_id', $organisation->id)
                ->where('subscription_id', $currentSubscription->id)
                ->get()
                ->groupBy('module_id');
        }

        return view('pages.settings.partials.modules', compact('availableModules', 'organisationModules', 'usageHistories'));
    }

    public function toggleModule(Request $request, $moduleId)
    {
        $organisation = Auth::user()->organisation;
        $module = \App\Models\Module::findOrFail($moduleId);
        $currentSubscription = $organisation->currentSubscription;

        if (!$currentSubscription) {
            return $this->ajaxResponse('error', 'No active subscription found.', []);
        }
        
        $orgModule = \App\Models\OrganisationModule::firstOrCreate([
            'organisation_id' => $organisation->id,
            'module_id' => $module->id
        ]);
        
        $isEnabled = !$orgModule->is_enabled;
        $orgModule->is_enabled = $isEnabled;

        if ($isEnabled) {
            $hasUsedEvaluation = \App\Models\OrganisationModuleUsageHistory::where('organisation_id', $organisation->id)
                ->where('module_id', $module->id)
                ->where('subscription_id', $currentSubscription->id)
                ->exists();

            \App\Models\OrganisationModuleUsageHistory::create([
                'organisation_id' => $organisation->id,
                'module_id' => $module->id,
                'enabled_at' => now(),
                'is_chargeable' => $hasUsedEvaluation,
                'subscription_id' => $currentSubscription->id
            ]);
        } else {
            $activeHistory = \App\Models\OrganisationModuleUsageHistory::where('organisation_id', $organisation->id)
                ->where('module_id', $module->id)
                ->where('subscription_id', $currentSubscription->id)
                ->whereNull('disabled_at')
                ->latest()
                ->first();

            if ($activeHistory) {
                $activeHistory->disabled_at = now();
                if (now()->diffInHours($activeHistory->enabled_at) >= 24) {
                    $activeHistory->is_chargeable = true;
                }
                $activeHistory->save();
            }
        }
        
        $orgModule->save();

        $actionWord = $isEnabled ? 'enabled' : 'disabled';

        return $this->ajaxResponse('success', "Module {$actionWord} successfully.", [
            'update' => [
                '#module-card-' . $module->id => [
                    'action' => 'replace-with',
                    'html' => view('pages.settings.partials.module-row', [
                        'module' => $module,
                        'isEnabled' => $orgModule->is_enabled
                    ])->render()
                ]
            ]
        ]);
    }

    public function updateOrganisationLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $organisation = Auth::user()->organisation;

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($organisation->logo) {
                Storage::disk('public')->delete($organisation->logo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $organisation->update(['logo' => $path]);
        }

        return $this->ajaxResponse('success', 'Logo updated successfully!', [
        ]);
    }

    public function getBilling()
    {
        $organisation = Auth::user()->organisation;
        $currentSubscription = $organisation->currentSubscription;
        
        return view('pages.settings.partials.billing', compact('currentSubscription'));
    }

    public function startSubscription()
    {
        $organisation = Auth::user()->organisation;
        $activeSub = $organisation->currentSubscription;

        if ($activeSub && in_array($activeSub->status, ['Trial', 'Cancelled'])) {
            $activeSub->update([
                'status' => 'Active',
                'type' => 'Monthly',
                'end_date' => null, // Ensure continuous life
                'remarks' => 'Activated Subscription'
            ]);
            
            return $this->ajaxResponse('success', 'Subscription activated successfully!', [
                'update' => [
                    '#settings-content-wrapper' => [
                        'action' => 'html',
                        'html' => view('pages.settings.partials.billing', ['currentSubscription' => $activeSub])->render()
                    ]
                ]
            ]);
        }

        return $this->ajaxResponse('error', 'No eligible trial or active subscripton found.', []);
    }

    public function cancelSubscription()
    {
        $organisation = Auth::user()->organisation;
        $activeSub = $organisation->currentSubscription;

        if ($activeSub && $activeSub->status === 'Active') {
            // Calculate end of billing cycle relative to anchor start_date
            $startDateDay = $activeSub->start_date->day;
            $currentDate = now();
            
            $endOfCycle = $currentDate->copy();
            if ($currentDate->day >= $startDateDay) {
                $endOfCycle->addMonth()->day($startDateDay)->subDay();
            } else {
                $endOfCycle->day($startDateDay)->subDay();
            }

            $activeSub->update([
                'status' => 'Cancelled',
                'end_date' => $endOfCycle->endOfDay(),
                'remarks' => 'Cancelled by user. Access runs through billing cycle.'
            ]);
            
            return $this->ajaxResponse('success', 'Subscription has been cancelled.', [
                'update' => [
                    '#settings-content-wrapper' => [
                        'action' => 'html',
                        'html' => view('pages.settings.partials.billing', ['currentSubscription' => $activeSub])->render()
                    ]
                ]
            ]);
        }
        
        return $this->ajaxResponse('error', 'Only active subscriptions can be cancelled.', []);
    }
}
