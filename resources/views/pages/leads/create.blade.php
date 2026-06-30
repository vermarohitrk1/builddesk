<form action="{{ route('leads.store') }}" method="POST" class="ajax-form" id="leadCreateForm">
    @csrf
    
    <div class="row">
        <div class="col-md-12">
            <div id="mobile-check-result" class="mt-1"></div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
            <input type="text" name="contact_mobile" id="contact_mobile" class="form-control" required placeholder="Enter mobile number">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
            <input type="text" name="contact_name" class="form-control" required placeholder="Enter customer name">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Alternate Mobile</label>
            <input type="text" name="alternate_mobile" class="form-control" placeholder="Optional">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="contact_email" class="form-control" placeholder="Optional">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Project Address</label>
        <textarea name="project_address" class="form-control" rows="2"></textarea>
    </div>

    <hr class="my-4">

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Lead Source</label>
            <select name="source" class="form-select">
                <option value="website">Website</option>
                <option value="facebook">Facebook</option>
                <option value="instagram">Instagram</option>
                <option value="google">Google</option>
                <option value="referral">Referral</option>
                <option value="builder">Builder</option>
                <option value="architect">Architect</option>
                <option value="walk_in">Walk In</option>
                <option value="other" selected>Other</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Lead Type</label>
            <select name="lead_type" class="form-select">
                <option value="residential">Residential</option>
                <option value="commercial">Commercial</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Project Type</label>
            <select name="project_type" class="form-select">
                <option value="">Select Project Type</option>
                <option value="villa">Villa</option>
                <option value="apartment">Apartment</option>
                <option value="office">Office</option>
                <option value="shop">Shop</option>
                <option value="hotel">Hotel</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Expected Budget</label>
            <input type="text" name="expected_budget" class="form-control" placeholder="e.g. 5-10 Lakhs">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Internal Notes</label>
        <textarea name="notes" class="form-control" rows="2"></textarea>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Lead</button>
    </div>
</form>
