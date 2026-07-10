@php
    $hasUsedEvaluation = false;
    if (Auth::check()) {
        $currentSubscription = Auth::user()->organisation->currentSubscription;
        if ($currentSubscription) {
            $hasUsedEvaluation = \App\Models\OrganisationModuleUsageHistory::where('organisation_id', Auth::user()->organisation_id)
                    ->where('module_id', $module->id)
                    ->where('subscription_id', $currentSubscription->id)
                    ->exists();
        }
    }

    if($isEnabled) {
        $confirmType = "info";
        $confirmMsg = "Disabling this module will remove access to its functionality. If this module has already become billable during the current billing cycle, disabling it will only prevent future billing cycles and will not remove the current invoice charge. Do you want to continue?";
    } else {
        if ($hasUsedEvaluation) {
            $confirmType = "warning";
            $confirmMsg = "You have already used the free evaluation for this module during the current billing cycle. Enabling this module will immediately include it in your monthly invoice. Do you want to continue?";
        } else {
            $confirmType = "success";
            $confirmMsg = "You are about to enable this module. You can evaluate it free for up to <strong>24 hours</strong>.<br> If you disable it within 24 hours and do not enable it again during the current billing cycle, it will not be included in your monthly invoice.<br> Otherwise, this module will be included in your current billing cycle invoice. <br><strong>Do you want to continue?</strong>";
        }
    }
@endphp

<div class="col-md-6 col-lg-4" id="module-card-{{ $module->id }}">
    <div class="card h-100 shadow-sm border-0">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="mb-1">{{ $module->name }}</h5>
                    <small class="text-muted"><code>{{ $module->slug }}</code></small>
                </div>

                @if($isEnabled)
                    <span class="badge bg-success">Enabled</span>
                @else
                    <span class="badge bg-secondary">Disabled</span>
                @endif
            </div>
        </div>

        <div class="card-footer bg-white border-0 pt-0">

            <div class="d-flex justify-content-between align-items-center">

                <span class="fw-semibold">
                    <small class="text-muted d-block">Monthly Price</small>
                    <span class="fw-semibold">{{ config('app.currency', '$') }}{{ number_format($module->price, 2) }}</span>
                </span>

                <a href="{{ route('settings.module.toggle', $module->id) }}"
                    class="ajax-link text-decoration-none"
                    data-method="POST"
                    data-confirm-type="{{ $confirmType }}"
                    data-confirm="{{ $confirmMsg }}"
                    data-confirm-title="{{ $isEnabled ? 'Disable Module' : 'Enable Module' }}">

                    <div class="form-check form-switch mb-0" style="pointer-events:none;">
                        <input class="form-check-input"
                                type="checkbox"
                                {{ $isEnabled ? 'checked' : '' }}
                                style="width:2.8em;height:1.4em;">
                    </div>

                </a>

            </div>

        </div>
    </div>
</div>
