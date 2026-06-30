@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Projects</h1>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped w-100" id="projects-table">
            <thead>
                <tr>
                    <th>Project Number</th>
                    <th>Client (Lead)</th>
                    <th>Started Date</th>
                    <th>Created By</th>
                    <th style="width: 150px;">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#projects-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('projects.datatable') }}",
        columns: [
            { data: 'project_number_link', name: 'project_number' },
            { data: 'client_name', name: 'lead.customer.name', orderable: false },
            { data: 'started_date', name: 'started_at' },
            { data: 'created_by_name', name: 'createdBy.name', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[2, 'desc']]
    });
});
</script>
@endpush
