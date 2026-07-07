@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0">Organisation Management</h1>
    <button type="button" class="btn btn-primary modal-trigger" data-url="{{ route('admin.organisations.create') }}" data-size="modal-lg" data-title="Add Organisation">
        <i class="fas fa-plus me-1"></i> Add Organisation
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle w-100" id="orgs-table">
                <thead class="table-light text-muted">
                    <tr>
                        <th width="60">Logo</th>
                        <th>Organisation Details</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Joined On</th>
                        <th class="text-end text-nowrap">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#orgs-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.organisations.datatable') }}",
        columns: [
            { data: 'logo', name: 'logo', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'contact_person', name: 'contact_person' },
            { data: 'phone', name: 'phone' },
            { data: 'active_status', name: 'active_status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        order: [[5, 'desc']]
    });
});
</script>
@endpush
