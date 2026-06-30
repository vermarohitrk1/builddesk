<form action="{{ route('expenses.update', $expense->id) }}" method="POST" class="ajax-form" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Expense Date <span class="text-danger">*</span></label>
            <input type="date" name="expense_date" class="form-control" required value="{{ $expense->expense_date->format('Y-m-d') }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select name="expense_category_id" class="form-select" required>
                <option value="">[Select Category]</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $expense->expense_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Supplier</label>
        <select name="supplier_id" id="expense-supplier-select" class="form-select">
            <option value="">[Select Supplier]</option>
            <option value="add_new_supplier" class="text-primary fw-bold">+ Add New Supplier</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" {{ $expense->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Amount <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required value="{{ $expense->amount }}">
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
            <select name="payment_method" class="form-select" required>
                <option value="cash" {{ $expense->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="bank" {{ $expense->payment_method == 'bank' ? 'selected' : '' }}>Bank</option>
                <option value="upi" {{ $expense->payment_method == 'upi' ? 'selected' : '' }}>UPI</option>
                <option value="card" {{ $expense->payment_method == 'card' ? 'selected' : '' }}>Card</option>
                <option value="cheque" {{ $expense->payment_method == 'cheque' ? 'selected' : '' }}>Cheque</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ $expense->description }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Attachment (Receipt/Invoice)</label>
        @if($expense->attachment)
        <div class="d-flex align-items-center mb-2 p-2 bg-light border rounded">
            <i class="fas fa-file-alt text-secondary me-2"></i>
            <span class="small text-truncate me-auto">{{ basename($expense->attachment) }}</span>
            <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="btn btn-sm btn-link text-primary"><i class="fas fa-external-link-alt"></i></a>
        </div>
        @endif
        <input type="file" name="attachment" class="form-control" accept="image/*,application/pdf">
        <div class="form-text">Uploading a new file will overwrite the existing one.</div>
    </div>

    <div class="text-end border-top pt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Expense</button>
    </div>
</form>

<!-- Inline Supplier Bootstrap Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="supplierModalLabel">Add New Supplier</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="supplier-errors"></div>
                <form id="inline-supplier-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Acme Materials Ltd.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile Number</label>
                        <input type="text" name="mobile" class="form-control" placeholder="10-digit mobile">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">GST Number</label>
                        <input type="text" name="gst_number" class="form-control" placeholder="Optional GSTIN">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Billing or office address"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional remarks"></textarea>
                    </div>
                    <div class="text-end border-top pt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="save-inline-supplier-btn">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


