<form action="{{ route('customers.store') }}" method="POST" class="ajax-form">
    @csrf

    <div class="mb-3">
        <label class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required placeholder="e.g. Rajesh Kumar">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="customer@example.com">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" placeholder="10-digit mobile number">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="3" placeholder="Full address (site / billing)"></textarea>
    </div>

    <div class="text-end border-top pt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Customer</button>
    </div>
</form>
