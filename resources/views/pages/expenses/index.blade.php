@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Expenses</h1>
    <button type="button" class="btn btn-primary modal-trigger" data-url="{{ route('expenses.create') }}" data-title="Record Expense" data-size="modal-md">
        <i class="fas fa-plus me-1"></i> Record Expense
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped w-100" id="expenses-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Created By</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#expenses-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('expenses.datatable') }}",
        columns: [
            { data: 'date', name: 'expense_date' },
            { data: 'category', name: 'category.name', orderable: false },
            { data: 'supplier', name: 'supplier.name', orderable: false },
            { data: 'amount_formatted', name: 'amount' },
            { data: 'payment_method_badge', name: 'payment_method', orderable: false },
            { data: 'created_by_name', name: 'creator.name', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endpush
