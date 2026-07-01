@extends('layouts.main')

@section('content')
<div class="mb-4">
    <nav class="breadcrumb-container" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item text-muted">Reports</li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Revenue Report</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h1>Revenue Report</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.revenue.pdf', request()->query()) }}" class="btn btn-outline-danger">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('reports.revenue.excel', request()->query()) }}" class="btn btn-outline-success">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
            <button onclick="window.print()" class="btn btn-outline-dark">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>
</div>

{{-- Filter Row --}}
<div class="card mb-4 border shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.revenue') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label font-weight-bold small text-muted">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label font-weight-bold small text-muted">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label font-weight-bold small text-muted">Status</label>
                <select name="status" class="form-select">
                    <option value="both" {{ $filters['status'] == 'both' ? 'selected' : '' }}>Confirmed & Completed</option>
                    <option value="confirmed" {{ $filters['status'] == 'confirmed' ? 'selected' : '' }}>Confirmed Only</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('reports.revenue') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card border shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Total Revenue</div>
                <h3 class="mb-0 text-primary fw-bold mt-2">₹ {{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card border shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Project Count</div>
                <h3 class="mb-0 text-dark fw-bold mt-2">{{ $projectCount }}</h3>
            </div>
        </div>
    </div>
</div>

{{-- Data Table --}}
<div class="card border shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">Project Number</th>
                        <th class="py-3">Customer Name</th>
                        <th class="py-3">Quotation Number</th>
                        <th class="py-3">Completion Date</th>
                        <th class="py-3">Created By</th>
                        <th class="text-end pe-4 py-3">Revenue Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $row)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">{{ $row['project_number'] }}</td>
                            <td>{{ $row['customer_name'] }}</td>
                            <td><span class="text-muted">{{ $row['quotation_number'] }}</span></td>
                            <td>{{ $row['completion_date'] }}</td>
                            <td>{{ $row['created_by'] }}</td>
                            <td class="text-end pe-4">₹ {{ number_format($row['revenue_amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No revenue data found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($reports) > 0)
                <tfoot>
                    <tr class="table-light fw-bold border-top border-dark">
                        <td class="ps-4">Total</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end pe-4 fw-bold">₹ {{ number_format($totalRevenue, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .breadcrumb-container, .btn, form {
        display: none !important;
    }
    .card, .table-responsive, table, table * {
        visibility: visible;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    table {
        width: 100% !important;
    }
}
</style>
@endsection
