@extends('layouts.main')

@section('content')
<div class="mb-4">
    <nav class="breadcrumb-container" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item text-muted">Reports</li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Financial Statement (Cash Flow)</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h1>Financial Statement (Cash Flow)</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.cashflow.pdf', request()->query()) }}" class="btn btn-outline-danger">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('reports.cashflow.excel', request()->query()) }}" class="btn btn-outline-success">
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
        <form method="GET" action="{{ route('reports.cashflow') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label font-weight-bold small text-muted">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
            </div>
            <div class="col-md-4">
                <label class="form-label font-weight-bold small text-muted">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="fas fa-filter me-1"></i> Filter Statement
                </button>
                <a href="{{ route('reports.cashflow') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border shadow-sm h-100 border-start border-success border-4">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Total In (Revenue)</div>
                <h3 class="mb-0 text-success fw-bold mt-2">₹ {{ number_format($totalIn, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border shadow-sm h-100 border-start border-danger border-4">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Total Out (Expenses)</div>
                <h3 class="mb-0 text-danger fw-bold mt-2">₹ {{ number_format($totalOut, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border shadow-sm h-100 border-start border-primary border-4">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Net Cash Flow (Profit/Loss)</div>
                <h3 class="mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} fw-bold mt-2">
                    ₹ {{ number_format($netProfit, 2) }}
                </h3>
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
                        <th class="ps-4 py-3">Date</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Reference</th>
                        <th class="py-3">Description</th>
                        <th class="text-end py-3 text-success">Money In (+)</th>
                        <th class="text-end py-3 text-danger">Money Out (-)</th>
                        <th class="text-end pe-4 py-3">Running Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $row)
                        <tr>
                            <td class="ps-4">{{ $row['date_formatted'] }}</td>
                            <td>
                                <span class="badge {{ $row['type'] == 'Revenue' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $row['type'] }}
                                </span>
                            </td>
                            <td class="fw-bold">{{ $row['reference'] }}</td>
                            <td><span class="text-muted small">{{ $row['description'] }}</span></td>
                            <td class="text-end text-success">
                                {{ $row['money_in'] > 0 ? '₹ ' . number_format($row['money_in'], 2) : '-' }}
                            </td>
                            <td class="text-end text-danger">
                                {{ $row['money_out'] > 0 ? '₹ ' . number_format($row['money_out'], 2) : '-' }}
                            </td>
                            <td class="text-end pe-4">₹ {{ number_format($row['balance'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No cash flow activity recorded for this period.</td>
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
                        <td class="text-end text-success fw-bold">₹ {{ number_format($totalIn, 2) }}</td>
                        <td class="text-end text-danger fw-bold">₹ {{ number_format($totalOut, 2) }}</td>
                        <td class="text-end pe-4 fw-bold">₹ {{ number_format($netProfit, 2) }}</td>
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
