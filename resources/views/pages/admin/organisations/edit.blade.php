<form action="{{ route('admin.organisations.update', $organisation->id) }}" method="POST" class="ajax-form" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Organisation Name *</label>
            <input type="text" name="name" class="form-control" value="{{ $organisation->name }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Company Logo <small class="text-muted">(Leave empty to keep current)</small></label>
            <input type="file" name="logo" class="form-control" accept="image/*">
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Contact Person</label>
            <input type="text" name="contact_person" class="form-control" value="{{ $organisation->contact_person }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ $organisation->email }}">
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="{{ $organisation->phone }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Subscription Plan</label>
            <select name="subscription_plan_id" class="form-select">
                <option value="">No Plan Selected</option>
                @foreach(\App\Models\SubscriptionPlan::where('is_active', true)->get() as $plan)
                    <option value="{{ $plan->id }}" {{ $organisation->subscription_plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
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
            <input type="text" name="address" class="form-control" value="{{ $organisation->address }}">
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" value="{{ $organisation->city }}">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">State/Province</label>
            <input type="text" name="state" class="form-control" value="{{ $organisation->state }}">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Country</label>
            <input type="text" name="country" class="form-control" value="{{ $organisation->country }}">
        </div>
        
        <div class="col-md-4 mb-3">
            <label class="form-label">GST/Tax Number</label>
            <input type="text" name="gst_number" class="form-control" value="{{ $organisation->gst_number }}">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Access Status *</label>
            <select name="active_status" class="form-select" required>
                <option value="active" {{ $organisation->active_status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="trial" {{ $organisation->active_status == 'trial' ? 'selected' : '' }}>Trial</option>
                <option value="suspended" {{ $organisation->active_status == 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>
        </div>
        <div class="col-md-4 mb-4">
            <label class="form-label">Payment Status</label>
            <select name="payment_status" class="form-select">
                <option value="paid" {{ $organisation->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="overdue" {{ $organisation->payment_status == 'overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
        </div>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary px-4">Update Organisation</button>
    </div>
</form>
