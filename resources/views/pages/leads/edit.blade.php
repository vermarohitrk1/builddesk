<form action="{{ route('leads.update', $lead->id) }}" method="POST" class="ajax-form" id="leadEditForm">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
            <input type="text" name="contact_mobile" class="form-control" value="{{ $lead->contact_mobile }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
            <input type="text" name="contact_name" class="form-control" value="{{ $lead->contact_name }}" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Alternate Mobile</label>
            <input type="text" name="alternate_mobile" class="form-control" value="{{ $lead->alternate_mobile }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="contact_email" class="form-control" value="{{ $lead->contact_email }}">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Project Address</label>
        <textarea name="project_address" class="form-control" rows="2">{{ $lead->project_address }}</textarea>
    </div>

    <hr class="my-4">

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Lead Source</label>
            <select name="source" class="form-select">
                @foreach(['website', 'facebook', 'instagram', 'google', 'referral', 'builder', 'architect', 'walk_in', 'other'] as $source)
                    <option value="{{ $source }}" {{ $lead->source == $source ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $source)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Lead Type</label>
            <select name="lead_type" class="form-select">
                <option value="residential" {{ $lead->lead_type == 'residential' ? 'selected' : '' }}>Residential</option>
                <option value="commercial" {{ $lead->lead_type == 'commercial' ? 'selected' : '' }}>Commercial</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Project Type</label>
            <select name="project_type" class="form-select">
                <option value="">Select Project Type</option>
                @foreach(['villa', 'apartment', 'office', 'shop', 'hotel'] as $type)
                    <option value="{{ $type }}" {{ $lead->project_type == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Expected Budget</label>
            <input type="text" name="expected_budget" class="form-control" value="{{ $lead->expected_budget }}">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Lead Status</label>
        <select name="status" class="form-select">
            @foreach([
                'new', 'contacted', 'site_visit_scheduled', 'measurement_pending', 
                'measurement_completed', 'quotation_sent', 'negotiation', 'confirmed', 'lost'
            ] as $status)
                <option value="{{ $status }}" {{ $lead->status == $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Internal Notes</label>
        <textarea name="notes" class="form-control" rows="2">{{ $lead->notes }}</textarea>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Lead</button>
    </div>
</form>
