@extends('layouts.main')

@section('content')
@if(isset($missingSalaryEmployees) && $missingSalaryEmployees->isNotEmpty())
    <div class="alert alert-warning shadow-sm border-warning mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning"></i>
            <div>
                <strong>Missing Salary Generation ({{ $lastMonthDate->format('F Y') }})</strong><br>
                The following employees do not have a salary generated for last month:
                <span class="fw-bold">{{ $missingSalaryEmployees->pluck('user.name')->join(', ') }}</span>
            </div>
        </div>
    </div>
@endif

<div class="mb-4">
    <nav class="breadcrumb-container" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item text-muted">HRM</li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Payroll</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h1>Payroll</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-primary modal-trigger"
                data-url="{{ route('payrolls.generate.modal') }}"
                data-title="Generate Payroll">
                <i class="fas fa-plus me-1"></i> Generate Payroll
            </button>
            <button id="mark-paid-btn" class="btn btn-success" disabled>
                <i class="fas fa-check-circle me-1"></i> Mark Selected as Paid
            </button>
            <a href="{{ route('payrolls.export', request()->query()) }}" class="btn btn-outline-success">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4 border shadow-sm">
    <div class="card-body">
        <form id="payroll-filter-form" class="row g-3">
            <div class="col-md-2">
                <label class="form-label small text-muted">Employee</label>
                <select name="employee_id" id="filter-employee" class="form-select form-select-sm">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Type</label>
                <select name="payroll_type" id="filter-type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach(\App\Models\Payroll::TYPES as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" id="filter-status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="generated">Generated</option>
                    <option value="paid">Paid</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Month</label>
                <select name="month" id="filter-month" class="form-select form-select-sm">
                    <option value="">All Months</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">Year</label>
                <select name="year" id="filter-year" class="form-select form-select-sm">
                    <option value="">All Years</option>
                    @foreach(range(date('Y'), 2024) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="button" id="apply-filter" class="btn btn-primary btn-sm flex-fill">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <button type="button" id="reset-filter" class="btn btn-outline-secondary btn-sm">Reset</button>
            </div>
        </form>
    </div>
</div>

{{-- DataTable --}}
<div class="card border shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="payrolls-table" class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Month / Year</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Generated Date</th>
                        <th>Paid Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Mark as Paid Modal --}}
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Select payment method for the selected payroll record(s):</p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                    <select id="pay-method" class="form-select">
                        <option value="bank" selected>Bank Transfer</option>
                        <option value="upi">UPI</option>
                        <option value="card">Card</option>
                        <option value="cheque">Cheque</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
                <div id="pay-alert" class="alert d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirm-pay-btn" class="btn btn-success">
                    <i class="fas fa-check-circle me-1"></i> Confirm Payment
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let table = null;
let selectedIds = [];

$(document).ready(function () {
    table = $('#payrolls-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('payrolls.data') }}',
            data: function (d) {
                d.employee_id  = $('#filter-employee').val();
                d.payroll_type = $('#filter-type').val();
                d.status       = $('#filter-status').val();
                d.month        = $('#filter-month').val();
                d.year         = $('#filter-year').val();
            }
        },
        columns: [
            {
                data: null, orderable: false, searchable: false,
                render: function (data) {
                    return '<input type="checkbox" class="form-check-input row-select" value="' + data.id + '">';
                }
            },
            { data: 'employee_name' },
            { data: 'payroll_type', render: d => d ? d.charAt(0).toUpperCase() + d.slice(1) : '' },
            { data: 'month_year' },
            { data: 'amount_formatted' },
            { data: 'status_badge', orderable: false },
            { data: 'payroll_date_formatted' },
            { data: 'paid_at_formatted' },
            { data: 'actions', orderable: false, className: 'text-center' },
        ]
    });

    // Filter
    $('#apply-filter').on('click', function () {
        table.ajax.reload();
    });

    $('#reset-filter').on('click', function () {
        $('#payroll-filter-form')[0].reset();
        table.ajax.reload();
    });

    // Select All
    $('#select-all').on('change', function () {
        const checked = this.checked;
        $('.row-select').prop('checked', checked);
        updateSelectedIds();
    });

    // Row checkboxes
    $('#payrolls-table').on('change', '.row-select', function () {
        updateSelectedIds();
    });

    function updateSelectedIds() {
        selectedIds = [];
        $('.row-select:checked').each(function () {
            selectedIds.push($(this).val());
        });
        $('#mark-paid-btn').prop('disabled', selectedIds.length === 0);
    }

    // Mark as Paid — open modal
    $('#mark-paid-btn').on('click', function () {
        if (selectedIds.length === 0) return;
        $('#pay-alert').addClass('d-none').text('');
        const modal = new bootstrap.Modal(document.getElementById('payModal'));
        modal.show();
    });

    // Confirm payment
    $('#confirm-pay-btn').on('click', function () {
        const method = $('#pay-method').val();
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');

        $.ajax({
            url: '{{ route('payrolls.mark-paid') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ids: selectedIds,
                payment_method: method
            },
            success: function (res) {
                if (res.status === 'success') {
                    bootstrap.Modal.getInstance(document.getElementById('payModal')).hide();
                    toastr.success(res.message);
                    table.ajax.reload();
                    selectedIds = [];
                    $('#mark-paid-btn').prop('disabled', true);
                    $('#select-all').prop('checked', false);
                } else {
                    $('#pay-alert').removeClass('d-none alert-success').addClass('alert alert-danger').text(res.message);
                }
            },
            error: function (xhr) {
                const res = xhr.responseJSON;
                $('#pay-alert').removeClass('d-none').addClass('alert alert-danger').text(res?.message || 'Something went wrong.');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Confirm Payment');
            }
        });
    });

    // Cancel payroll
    $('#payrolls-table').on('click', '.cancel-payroll', function () {
        const id = $(this).data('id');
        if (!confirm('Are you sure you want to cancel this payroll record?')) return;

        $.ajax({
            url: '/payrolls/' + id + '/cancel',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                if (res.status === 'success') {
                    toastr.success(res.message);
                    table.ajax.reload();
                } else {
                    toastr.error(res.message);
                }
            }
        });
    });
});
</script>
@endpush
@endsection
