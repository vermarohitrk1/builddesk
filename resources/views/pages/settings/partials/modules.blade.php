<div class="card bg-white shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 fw-bold">Business Modules Configuration</h5>
    </div>
    
    <div class="card-body py-4">
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i> Enable or disable optional business modules for your organisation.
        </div>

        <div class="row g-4">
            @forelse($availableModules as $module)
                @php
                    $orgModule = $organisationModules->get($module->id);
                    $isEnabled = $orgModule ? $orgModule->is_enabled : false;
                @endphp
                @include('pages.settings.partials.module-row', [
                    'module' => $module,
                    'isEnabled' => $isEnabled
                ])
            @empty
                <div class="col-12">
                    <div class="text-center text-muted py-4">
                        No optional modules available.
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>
