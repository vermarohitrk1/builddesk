<form action="{{ route('quotations.store') }}" method="POST" class="ajax-form">
    @csrf
    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Quotation Number <span class="text-danger">*</span></label>
            <input type="text" name="quotation_number" class="form-control" value="QTN-{{ strtoupper(uniqid()) }}" readonly>
        </div>
    </div>

    <div class="mb-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Quotation Items</h6>
        <button type="button" class="btn btn-sm btn-outline-primary add-item-row" data-url="{{ route('quotations.add-item-row') }}">
            <i class="fas fa-plus me-1"></i> Add Item
        </button>
    </div>

    <div id="items-container">
        <!-- Default Row -->
        @include('pages.quotations.components.item')
    </div>

    <div class="row mt-3 justify-content-end">
        <div class="col-md-5">
            <!-- <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                <span class="text-muted">Sub Total</span>
                <span class="fw-bold" id="sub-total">₹ 0.00</span>
                <input type="hidden" name="sub_total" id="sub_total_val" value="0">
            </div> -->
            
            <div class="mb-3 d-flex align-items-center">
                <label class="form-label fw-bold me-2">Discount</label>
                <div class="input-group input-group-sm">
                    <select name="discount_type" id="discount_type" class="form-select form-select-sm" style="max-width: 100px;">
                        <option value="fixed"> ₹ (Rupees)</option>
                        <option value="percentage"> % (Percent)</option>
                    </select>
                    <input type="number" name="discount_value" id="discount_value" class="form-control" value="0" step="0.01">
                </div>
            </div>
            <div class="d-flex justify-content-between border-bottom pb-2">
                <span class="fw-bold">Total Amount</span>
                <span class="fw-bold" id="grand-total">₹ 0.00</span>
                <input type="hidden" name="total_amount" id="total_amount_val" value="0">
            </div>
        </div>
    </div>

    <div class="mb-3 mt-3">
        <label class="form-label">Terms & Notes</label>
        <textarea name="terms" class="form-control" rows="3" placeholder="Payment terms, validity etc."></textarea>
    </div>

    <div class="text-end mt-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Generate Quotation</button>
    </div>
</form>
