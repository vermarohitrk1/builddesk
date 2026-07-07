<form action="{{ route('admin.organisations.store') }}" method="POST" class="ajax-form" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Organisation Name *</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Acme Studio">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Company Logo</label>
            <input type="file" name="logo" class="form-control" accept="image/*">
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Contact Person</label>
            <input type="text" name="contact_person" class="form-control" placeholder="e.g. John Doe">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="e.g. contact@acme.com">
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Phone Number</label>
            <input type="text" name="phone" class="form-control" placeholder="e.g. +1 555-0198">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Subscription Plan</label>
            <select name="subscription_plan_id" class="form-select">
                <option value="">No Plan Selected</option>
                @foreach(\App\Models\SubscriptionPlan::where('is_active', true)->get() as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Location Information -->
    <hr class="text-muted">
    <h6 class="fw-bold mb-3">Location & Billing</h6>
    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Street Address</label>
            <input type="text" name="address" class="form-control" placeholder="e.g. 123 Business Rd">
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">State/Province</label>
            <input type="text" name="state" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Country</label>
            <input type="text" name="country" class="form-control">
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">GST/Tax Number</label>
            <input type="text" name="gst_number" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Access Status *</label>
            <select name="active_status" class="form-select" required>
                <option value="active">Active</option>
                <option value="trial">Trial</option>
                <option value="suspended">Suspended</option>
            </select>
        </div>
        <div class="col-md-4 mb-4">
            <label class="form-label">Payment Status</label>
            <select name="payment_status" class="form-select">
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
            </select>
        </div>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary px-4">Save Organisation</button>
    </div>
</form>
