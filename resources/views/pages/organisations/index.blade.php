@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Organisations</h1>
    <button type="button" class="btn btn-primary modal-trigger" data-url="{{ route('organisations.create') }}" data-title="Add Organisation">
        Add Organisation
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped w-100" id="orgs-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#orgs-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('organisations.datatable') }}",
        columns: [
            { data: 'name', name: 'name' },
            { data: 'plan', name: 'plan', orderable: false, searchable: false },
            { data: 'active_status', name: 'active_status' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
