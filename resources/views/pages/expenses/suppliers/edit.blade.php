<div class="mb-3">
    <label class="form-label fw-bold">Supplier Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Mobile</label>
        <input type="text" name="mobile" class="form-control" value="{{ $supplier->mobile }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">GST Number</label>
        <input type="text" name="gst_number" class="form-control" value="{{ $supplier->gst_number }}">
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control" rows="2">{{ $supplier->address }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Notes</label>
    <textarea name="notes" class="form-control" rows="2">{{ $supplier->notes }}</textarea>
</div>
<form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" class="ajax-form d-none">
    @csrf
    @method('PUT')
</form>
