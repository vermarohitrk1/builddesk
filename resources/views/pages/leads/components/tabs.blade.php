
    <!-- Measurements Tab -->
    <div class="tab-pane fade {{ $activeTab === 'measurements' ? 'active show' : '' }}" id="measurements" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Measurement Records</h6>
            <button class="btn btn-sm btn-primary modal-trigger" data-url="{{ route('measurements.create', ['lead_id' => $lead->id]) }}" data-title="Add Measurement" data-size="modal-lg">
                <i class="fas fa-plus me-1"></i> Add Measurement
            </button>
        </div>
        
        @forelse($lead->measurements as $m)
        <div class="card mb-3 border">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <div>
                    <span class="fw-bold">{{ $m->title }}</span>
                    <span class="text-muted small ms-2">{{ $m->created_at->format('d M Y') }}</span>
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-link text-primary p-0 me-2 modal-trigger" 
                        data-url="{{ route('measurements.edit', $m->id) }}" 
                        data-title="Edit Measurement" 
                        data-size="modal-lg">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="{{ route('measurements.destroy', $m->id) }}" 
                        class="btn btn-sm btn-link text-danger p-0 ajax-link" 
                        data-method="DELETE" 
                        data-confirm="Are you sure you want to delete this measurement?">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($m->items as $item)
                        <tr>
                            <td class="ps-3" style="width: 30%">{{ $item->title }}</td>
                            <td class="text-muted">{{ $item->description }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($m->notes)
                <div class="p-2 small bg-white border-top">
                    <i class="fas fa-sticky-note me-1 text-warning"></i> {{ $m->notes }}
                </div>
                @endif
            </div>
        </div>
        @empty
            <div class="text-center py-4 text-muted border border-dashed">No Measurement recorded yet.</div>
        @endforelse
    </div>

    <!-- Quotations Tab -->
    <div class="tab-pane fade {{ $activeTab === 'quotations' ? 'active show' : '' }}" id="quotations" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Quotations</h6>
            <button class="btn btn-sm btn-primary modal-trigger" data-url="{{ route('quotations.create', ['lead_id' => $lead->id]) }}" data-title="Create Quotation" data-size="modal-lg">
                <i class="fas fa-plus me-1"></i> New Quotation
            </button>
        </div>

        @forelse($lead->quotations as $q)
        <div class="card mb-3 border">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <div>
                    <span class="fw-bold me-2">{{ $q->quotation_number }}</span>
                    <span class="badge bg-info small">{{ ucfirst($q->status) }}</span>
                    <span class="text-muted small ms-2">{{ $q->created_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    {{-- PDF Actions --}}
                    <!-- <a href="{{ route('quotations.pdf.preview', $q->id) }}" target="_blank"
                        class="btn btn-sm btn-outline-secondary" title="Preview PDF">
                        <i class="fas fa-eye"></i>
                    </a> -->
                    <a href="{{ route('quotations.pdf.download', $q->id) }}"
                        class="btn btn-sm btn-outline-dark" title="Download PDF">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                    {{-- Edit / Delete --}}
                    <button class="btn btn-sm btn-link text-primary modal-trigger" 
                        data-url="{{ route('quotations.edit', $q->id) }}" 
                        data-title="Edit Quotation" 
                        data-size="modal-lg" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="{{ route('quotations.destroy', $q->id) }}" 
                        class="btn btn-sm btn-link text-danger ajax-link" 
                        data-method="DELETE" 
                        data-confirm="Are you sure you want to delete this quotation?" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($q->items as $qItem)
                        <tr>
                            <td class="ps-3">{{ $qItem->description }}</td>
                            <td class="text-end pe-3">₹ {{ number_format($qItem->amount, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td class="ps-3">
                                Sub Total:<br>
                                Discount:<br>
                                Total:
                            </td>
                            <td class="text-end pe-3">
                                <span class="fw-bold">₹ {{ number_format($q->sub_total, 2) }}</span><br>
                                <span class="fw-bold">₹ {{ number_format($q->discount_amount, 2) }}</span><br>
                                <span class="fw-bold text-primary">₹ {{ number_format($q->total_amount, 2) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @empty
            <div class="text-center py-4 text-muted border border-dashed">No quotations generated yet.</div>
        @endforelse
    </div>

    <!-- Follow-ups Tab -->
    <div class="tab-pane fade {{ $activeTab === 'followups' ? 'active show' : '' }}" id="followups" role="tabpanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Follow-up Tasks</h6>
            <button class="btn btn-sm btn-primary modal-trigger" data-url="{{ route('followups.create', ['lead_id' => $lead->id]) }}" data-title="Add Follow-up" data-size="modal-md">
                <i class="fas fa-plus me-1"></i> Add Follow-up
            </button>
        </div>

        @forelse($lead->followups as $f)
        <div class="card mb-3 border">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <div>
                    <span class="text-muted small me-2">Follow-up Date:</span>
                    <span class="fw-bold">{{ $f->followup_date->format('d M Y - h:i A') }}</span>
                    @if($f->completed_at)
                    <span class="badge bg-success ms-2">Completed</span>
                    @else
                    <span class="badge bg-warning text-dark ms-2">Pending</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-1">
                    {{-- Complete --}}
                    @if(!$f->completed_at)
                    <a href="{{ route('followups.complete', $f->id) }}"
                        class="btn btn-sm btn-link text-success ajax-link"
                        data-method="POST"
                        title="Mark Complete">
                        <i class="fas fa-check text-success"></i>
                    </a>
                    @endif

                    {{-- Edit --}}
                    <button class="btn btn-sm btn-link text-primary modal-trigger"
                        data-url="{{ route('followups.edit', $f->id) }}"
                        data-title="Edit Follow-up"
                        data-size="modal-md"
                        title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>

                    {{-- Delete --}}
                    <a href="{{ route('followups.destroy', $f->id) }}"
                        class="btn btn-sm btn-link text-danger ajax-link"
                        data-method="DELETE"
                        data-confirm="Are you sure you want to delete this follow-up?"
                        title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-3">
                <p class="mb-2 text-dark">{{ $f->note }}</p>
                <div class="text-muted small border-top pt-2 d-flex justify-content-between">
                    <span>Created By: {{ $f->user ? $f->user->name : 'System' }}</span>
                    <span>Created Date: {{ $f->created_at->format('d M Y h:i A') }}</span>
                </div>
            </div>
        </div>
        @empty
            <div class="text-center py-4 text-muted border border-dashed">No Follow-up tasks recorded yet.</div>
        @endforelse
    </div>

    <!-- Notes Tab -->
    <div class="tab-pane fade {{ $activeTab === 'notes' ? 'active show' : '' }}" id="notes" role="tabpanel">
        <h6>Internal Notes</h6>
        <div class="bg-light p-3 border">
            {{ $lead->notes ?: 'No notes available.' }}
        </div>
    </div>