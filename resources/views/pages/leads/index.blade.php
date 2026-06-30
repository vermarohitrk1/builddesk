@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Leads</h1>
    <button type="button" 
        class="btn btn-primary modal-trigger" 
        data-url="{{ route('leads.create') }}" 
        data-size="modal-lg" 
        data-title="Add Lead">
        Add Lead
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped w-100" id="leads-table">
            <thead>
                <tr>
                    <th>Contact Info</th>
                    <th>Address</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Leads table
    const datatable = $('#leads-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('leads.datatable') }}",
        columns: [
            { data: 'contact', name: 'contact_name' },
            { data: 'project_address', name: 'project_address' },
            { data: 'source', name: 'source' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', searchable: false, orderable: false }
        ],
        order: [[3, 'desc']]
    });

    // Check mobile number
    let checkTimeout = null;

    $(document).on('input', '#contact_mobile', function() {
        const mobile = $(this).val();
        const resultDiv = $('#mobile-check-result');
        
        clearTimeout(checkTimeout);
        
        if (mobile.length < 10) {
            resultDiv.html('');
            return;
        }

        checkTimeout = setTimeout(() => {
            $.get('{{ route("leads.check-mobile") }}', { mobile: mobile }, function(response) {
                if (response.status === 'exists') {
                    let html = `<div class="alert alert-warning py-2 mb-0 mt-2" style="font-size: 0.85rem;">
                        <i class="fas fa-exclamation-triangle me-2"></i> ${response.message}<br>
                        <div class="mt-2">`;
                    
                    if (response.lead_id) {
                        html += `<a href="#" class="btn btn-xs btn-dark me-2">Open Existing Lead</a>`;
                    }
                    html += `<span class="text-muted">You can still create a "New Opportunity" by proceeding.</span></div></div>`;
                    resultDiv.html(html);
                } else {
                    resultDiv.html('<small class="text-success"><i class="fas fa-check-circle"></i> New Mobile Number</small>');
                }
            });
        }, 500);
    });
});
</script>
@endpush
