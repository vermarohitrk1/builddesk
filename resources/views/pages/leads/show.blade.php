@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                @if($lead->project)
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $lead->project->project_number }}</li>
                @else
                    <li class="breadcrumb-item"><a href="{{ route('leads.index') }}">Leads</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $lead->contact_name }}</li>
                @endif
            </ol>
        </nav>
        <h1 class="h3 mb-0">{{ $lead->contact_name }}</h1>
    </div>
    <div class="btn-group">
        @if(!$lead->project)
        <a href="{{ route('projects.store') }}"
            class="btn btn-success ajax-link me-2"
            data-method="POST"
            data-data='{"lead_id": {{ $lead->id }}}'
            data-confirm="Start a project for this lead? The lead status will be updated to Confirmed.">
            <i class="fas fa-rocket me-1"></i> Start Project
        </a>
        @endif
        <button class="btn btn-outline-primary modal-trigger" data-url="{{ route('leads.edit', $lead->id) }}" data-title="Edit Lead" data-size="modal-lg">
            <i class="fas fa-edit me-1"></i> Edit Lead
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Lead Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small d-block">Status</label>
                    @php
                        $statusClass = [
                            'new'                    => 'bg-info',
                            'contacted'              => 'bg-primary',
                            'site_visit_scheduled'   => 'bg-warning text-dark',
                            'measurement_pending'    => 'bg-warning',
                            'measurement_completed'  => 'bg-success',
                            'quotation_sent'         => 'bg-info',
                            'negotiation'            => 'bg-primary',
                            'lost'                   => 'bg-danger',
                            'confirmed'              => 'bg-success',
                        ][$lead->status] ?? 'bg-secondary';
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Mobile</label>
                    <span class="fw-bold">{{ $lead->contact_mobile }}</span>
                </div>
                @if($lead->contact_email)
                <div class="mb-3">
                    <label class="text-muted small d-block">Email</label>
                    <span>{{ $lead->contact_email }}</span>
                </div>
                @endif
                <div class="mb-3">
                    <label class="text-muted small d-block">Source</label>
                    <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Project Type</label>
                    <span>{{ ucfirst($lead->project_type) }} ({{ ucfirst($lead->lead_type) }})</span>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="text-muted small d-block">Project Address</label>
                    <p class="mb-0">{{ $lead->project_address ?: 'Not provided' }}</p>
                </div>

                @if($lead->project)
                <hr>
                <div class="p-3 rounded border border-success bg-light">
                    <div class="fw-bold text-success mb-2"><i class="fas fa-check-circle me-1"></i> Project Active</div>
                    <div class="mb-1 small">
                        <span class="text-muted">Project #:</span>
                        <strong class="ms-1">{{ $lead->project->project_number }}</strong>
                    </div>
                    <div class="mb-1 small">
                        <span class="text-muted">Started:</span>
                        <strong class="ms-1">{{ $lead->project->started_at->format('d M Y') }}</strong>
                    </div>
                    <div class="small">
                        <span class="text-muted">By:</span>
                        <strong class="ms-1">{{ $lead->project->createdBy?->name ?? 'System' }}</strong>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs border-0" id="leadTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-0 border-0 py-3 px-4" id="measurements-tab" data-bs-toggle="tab" data-bs-target="#measurements" type="button" role="tab">
                            <i class="fas fa-ruler-combined me-2"></i> Measurements
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-0 border-0 py-3 px-4" id="quotations-tab" data-bs-toggle="tab" data-bs-target="#quotations" type="button" role="tab">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Quotations
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-0 border-0 py-3 px-4" id="followups-tab" data-bs-toggle="tab" data-bs-target="#followups" type="button" role="tab">
                            <i class="fas fa-calendar-check me-2"></i> Follow-ups
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-0 border-0 py-3 px-4" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">
                            <i class="fas fa-sticky-note me-2"></i> Internal Notes
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="leadTabsContent">
                    @include('pages.leads.components.tabs', ['activeTab' => 'measurements', 'lead' => $lead])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

