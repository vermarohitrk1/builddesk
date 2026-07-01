@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Customers</h1>
    <button type="button"
        class="btn btn-primary modal-trigger"
        data-url="{{ route('customers.create') }}"
        data-title="Add Customer"
        data-size="modal-md">
        <i class="fas fa-plus me-1"></i> Add Customer
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped w-100" id="customers-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#customers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('customers.datatable') }}",
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'address', name: 'address', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']]
    });
});
</script>
@endpush
