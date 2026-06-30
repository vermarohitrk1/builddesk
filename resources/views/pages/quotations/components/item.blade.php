
    <div class="quotation-item item-row border p-2 mb-2 bg-light">
        <input type="hidden" name="items[]" value="{{ $item->id ?? '' }}">
        <div class="row g-2 align-items-start">
            <div class="col-md-8">
                <div class="input-group input-group-sm">
                    <input type="text" name="item_description[]" class="form-control q-desc" placeholder="Item Description (e.g. Living Room Windows)" value="{{ $item->description ?? '' }}" required autocomplete="off">
                    <button type="button" class="btn btn-outline-secondary suggest-btn" title="View Measurement Notes" tabindex="-1">
                        <i class="fas fa-lightbulb text-warning"></i>
                    </button>
                </div>
                {{-- Suggestions Panel - collapsed by default --}}
                <div class="measurement-suggestions-panel d-none mt-1 border rounded bg-white shadow-sm" style="position:relative; z-index:100;">
                    <div class="d-flex justify-content-between align-items-center px-2 py-1 border-bottom bg-light">
                        <small class="text-muted fw-semibold"><i class="fas fa-lightbulb text-warning me-1"></i>Measurement Notes</small>
                        <button type="button" class="btn btn-sm btn-link text-secondary p-0 close-suggestions" title="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="suggestions-list" style="max-height: 180px; overflow-y: auto;">
                        <div class="text-center text-muted py-3 small loading-indicator"><i class="fas fa-spinner fa-spin me-1"></i> Loading...</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <input type="number" name="item_amount[]" class="form-control form-control-sm q-amount" placeholder="Amount" step="0.01" value="{{ $item->amount ?? '' }}" required>
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn btn-sm btn-link text-danger remove-row" style="display:none;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>