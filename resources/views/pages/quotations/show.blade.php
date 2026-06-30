@extends('layouts.main')

@section('content')
<div class="mb-4">
    <nav class="breadcrumb-container" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}" class="text-decoration-none text-muted">Quotations</a></li>
            <li class="breadcrumb-item active text-primary" aria-current="page">{{ $quotation->quotation_number }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h1>Quotation: {{ $quotation->quotation_number }}</h1>
        <div>
            <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 md-12">
        <div class="card mb-4 border">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <div>
                    <span class="badge bg-info small me-2">{{ ucfirst($quotation->status) }}</span>
                    <span class="text-muted small">Created: <strong>{{ $quotation->created_at->format('d M Y') }}</strong></span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    {{-- PDF Actions --}}
                    <a href="{{ route('quotations.pdf.download', $quotation->id) }}"
                        class="btn btn-sm btn-outline-dark" title="Download PDF">
                        <i class="fas fa-file-pdf me-1"></i> Download PDF
                    </a>

                    {{-- Edit / Delete --}}
                    <button class="btn btn-sm btn-outline-primary modal-trigger" 
                        data-url="{{ route('quotations.edit', $quotation->id) }}" 
                        data-title="Edit Quotation" 
                        data-size="modal-lg" title="Edit">
                        <i class="fas fa-edit me-1"></i> Edit
                    </button>
                    <a href="{{ route('quotations.destroy', $quotation->id) }}" 
                        class="btn btn-sm btn-outline-danger ajax-link" 
                        data-method="DELETE" 
                        data-confirm="Are you sure you want to delete this quotation?" title="Delete">
                        <i class="fas fa-trash me-1"></i> Delete
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Item / Description</th>
                            <th class="text-end pe-4" style="width: 200px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items as $qItem)
                        <tr>
                            <td class="ps-4 py-3">{{ $qItem->description }}</td>
                            <td class="text-end pe-4 py-3">₹ {{ number_format($qItem->amount, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="table-light border-top-2">
                            <td class="text-end ps-4 py-2"><strong>Sub Total:</strong></td>
                            <td class="text-end pe-4 py-2">₹ {{ number_format($quotation->sub_total, 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="text-end ps-4 py-2"><strong>Discount:</strong></td>
                            <td class="text-end pe-4 py-2">₹ {{ number_format($quotation->discount_amount, 2) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="text-end ps-4 py-2"><strong>Total Amount:</strong></td>
                            <td class="text-end pe-4 py-2 text-primary font-weight-bold" style="font-size: 1.1rem;">
                                <strong>₹ {{ number_format($quotation->total_amount, 2) }}</strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if($quotation->terms)
            <div class="card-footer bg-white p-3 border-top">
                <h6 class="text-muted small text-uppercase">Terms & Conditions</h6>
                <div class="small p-2 bg-light border rounded text-dark pre-wrap">{{ $quotation->terms }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="col-lg-4 md-12">
        <div class="card border mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">Client / Contact Details</h6>
            </div>
            <div class="card-body">
                @if($quotation->customer)
                <div class="mb-3">
                    <label class="text-muted small d-block">Client Name</label>
                    <span class="fw-bold">{{ $quotation->customer->name }}</span>
                </div>
                @endif

                @if($quotation->lead)
                <div class="mb-3">
                    <label class="text-muted small d-block">Contact Person</label>
                    <span>{{ $quotation->lead->contact_name }}</span>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Mobile Number</label>
                    <a href="tel:{{ $quotation->lead->contact_mobile }}" class="text-decoration-none">{{ $quotation->lead->contact_mobile }}</a>
                </div>
                @if($quotation->lead->project_address)
                <div class="mb-3">
                    <label class="text-muted small d-block">Project Site Address</label>
                    <span>{{ $quotation->lead->project_address }}</span>
                </div>
                @endif
                <div class="border-top pt-2">
                    <a href="{{ route('leads.show', $quotation->lead->id) }}" class="btn btn-sm btn-link p-0 text-primary">
                        <i class="fas fa-arrow-right me-1"></i> View Full Lead Timeline
                    </a>
                </div>
                @else
                <p class="text-muted mb-0 small">No lead associated with this quotation.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
