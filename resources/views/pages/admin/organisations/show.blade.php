@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Manage Organisation: {{ $organisation->name }}</h1>
    <a href="{{ route('admin.organisations.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <ul class="nav nav-tabs border-bottom-0" id="orgTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-4" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">Basic Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-4" id="modules-tab" data-bs-toggle="tab" data-bs-target="#modules" type="button" role="tab">Modules & Billing</button>
            </li>
        </ul>
    </div>
    <div class="card-body p-4 pt-3 border-top">
        <div class="tab-content" id="orgTabsContent">
            
            <!-- Basic Details Tab -->
            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                @include('pages.admin.organisations.edit')
                
                @push('scripts')
                <script>
                    $(document).ready(function() {
                        // Make the form submit button text more contextual for the page and hide modal close
                        $('#basic form .btn-secondary').hide();
                    });
                </script>
                @endpush
            </div>
            
            <!-- Modules & Billing Tab -->
            <div class="tab-pane fade" id="modules" role="tabpanel">
                <form action="{{ route('admin.organisations.modules.update', $organisation->id) }}" method="POST">
                    @csrf
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i> Configure the modules available to this organisation and set their monthly pricing.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Service / Module</th>
                                    <th width="200" class="text-center">Monthly Price ({{ config('app.currency', '$') }})</th>
                                    <th width="150" class="text-center">Access</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableModules as $module)
                                @php
                                    $orgModule = $organisationModules->get($module->id);
                                    $price = $module->price;
                                    $isEnabled = $orgModule ? $orgModule->is_enabled : false;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $module->name }}</div>
                                        <small class="text-muted"><code>{{ $module->slug }}</code></small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="fw-bold">{{ config('app.currency', '$') }}{{ number_format($price, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input class="form-check-input" type="checkbox" name="modules[{{ $module->id }}][is_enabled]" value="1" id="module_{{ $module->id }}" {{ $isEnabled ? 'checked' : '' }} style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">Save Module Configuration</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
@endsection
