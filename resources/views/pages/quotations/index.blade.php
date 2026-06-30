@extends('layouts.main')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quotations</h1>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped w-100" id="quotations-table">
            <thead>
                <tr>
                    <th>Quotation Number</th>
                    <th>Client</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Created Date</th>
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
    $('#quotations-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('quotations.datatable') }}",
        columns: [
            { data: 'quotation_number_link', name: 'quotation_number' },
            { data: 'client_name', name: 'customer.name', orderable: false },
            { data: 'amount_formatted', name: 'total_amount' },
            { data: 'status_badge', name: 'status' },
            { data: 'created_date', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[4, 'desc']]
    });
});
</script>
@endpush
