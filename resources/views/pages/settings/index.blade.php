@extends('layouts.main')

@section('content')
<div class="mb-4">
    <h1>Settings</h1>
    <p class="text-muted">Manage your organisation profile, preferences, and master data.</p>
</div>

<div class="row">
    <!-- Left Permanent Sidebar -->
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded" id="settings-sidebar" role="tablist">
                    <a class="list-group-item list-group-item-action active fw-bold py-3" 
                       id="menu-basic" data-bs-toggle="list" href="#content-area" role="tab"
                       onclick="loadSettingsTab('{{ route('settings.basic') }}')">
                        <i class="fas fa-cog fa-fw me-2"></i> Basic Settings
                    </a>
                    
                    <a class="list-group-item list-group-item-action fw-bold py-3" 
                       id="menu-categories" data-bs-toggle="list" href="#content-area" role="tab"
                       onclick="loadSettingsTab('{{ route('settings.expense_categories') }}')">
                        <i class="fas fa-tags fa-fw me-2"></i> Expense Categories
                    </a>

                    <a class="list-group-item list-group-item-action text-muted py-3 disabled" role="tab">
                        <i class="fas fa-briefcase fa-fw me-2"></i> Business Configuration 
                        <br><small class="ms-4">(Coming Soon)</small>
                    </a>
                    
                    <a class="list-group-item list-group-item-action text-muted py-3 disabled" role="tab">
                        <i class="fas fa-ellipsis-h fa-fw me-2"></i> More...
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Content Area -->
    <div class="col-md-9">
        <div id="settings-content-wrapper" class="tab-content">
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2 text-muted">Loading settings...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Load default tab
        loadSettingsTab('{{ route('settings.basic') }}');
    });

    function loadSettingsTab(url) {
        let wrapper = $('#settings-content-wrapper');
        wrapper.html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
        
        $.get(url, function(res) {
            wrapper.html(res);
        }).fail(function() {
            wrapper.html('<div class="alert alert-danger">Failed to load content. Please try again.</div>');
        });
    }
</script>
@endpush
@endsection
