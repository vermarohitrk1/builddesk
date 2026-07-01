<div class="mb-3">
    <label class="form-label fw-bold">Supplier Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" placeholder="Company or individual name" required>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Mobile</label>
        <input type="text" name="mobile" class="form-control" placeholder="+91 9876543210">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">GST Number</label>
        <input type="text" name="gst_number" class="form-control" placeholder="e.g. 22ABCDE1234F1Z5">
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control" rows="2" placeholder="Full address..."></textarea>
</div>
<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="2" placeholder="Internal notes..."></textarea>
</div>
<form action="{{ route('suppliers.store') }}" method="POST" class="ajax-form d-none">
    @csrf
</form>
