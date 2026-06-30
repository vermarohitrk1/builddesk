@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>HRM: Employees</h1>
    <button type="button" class="btn btn-primary modal-trigger" data-url="{{ route('employees.create') }}" data-title="Register Employee" data-size="modal-lg">
        Add Employee
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped w-100" id="employees-table">
            <thead>
                <tr>
                    <th>Emp Code</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Designation</th>
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
    $('#employees-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('employees.datatable') }}",
        columns: [
            { data: 'employee_code', name: 'employee_code' },
            { data: 'name', name: 'name', orderable: false },
            { data: 'email', name: 'email', orderable: false },
            { data: 'designation', name: 'designation' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush
