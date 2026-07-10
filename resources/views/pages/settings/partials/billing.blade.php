<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h5 class="mb-0">Subscription Overview</h5>
    </div>
    <div class="card-body">
        
        @if(!$currentSubscription)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i> No active subscription found for your organisation.
            </div>
        @else
            <div class="row mb-4">
                <div class="col-md-3">
                    <p class="text-muted mb-1">Status</p>
                    <span class="badge {{ $currentSubscription->status === 'Active' ? 'bg-success' : ($currentSubscription->status === 'Trial' ? 'bg-warning text-dark' : ($currentSubscription->status === 'Cancelled' ? 'bg-danger' : 'bg-secondary')) }} fs-6">
                        {{ $currentSubscription->status }}
                    </span>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Plan Type</p>
                    <span class="fw-bold fs-6">{{ $currentSubscription->type }}</span>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1">Billing Period</p>
                    <span class="fw-semibold">
                        {{ $currentSubscription->start_date ? $currentSubscription->start_date->format('M d, Y') : '-' }} <br>to<br> 
                        {{ $currentSubscription->end_date ? $currentSubscription->end_date->format('M d, Y') : 'Lifetime' }}
                    </span>
                </div>
                <div class="col-md-3">
                    @if($currentSubscription->grace_until)
                        <p class="text-muted mb-1">Grace Period</p>
                        <span class="text-danger fw-bold">Until {{ $currentSubscription->grace_until->format('M d, Y') }}</span>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-end border-top pt-3">
                @if($currentSubscription->status === 'Trial' || $currentSubscription->status === 'Cancelled')
                    <a href="{{ route('settings.subscription.start') }}" 
                       class="ajax-link btn btn-primary"
                       data-method="POST"
                       data-confirm-type="info"
                       data-confirm="Are you sure you want to start a full subscription? This will convert your trial into a standard monthly billing cycle."
                       data-confirm-title="Start Subscription">
                        <i class="fas fa-rocket me-2"></i> Start Subscription
                    </a>
                @elseif($currentSubscription->status === 'Active')
                    <a href="{{ route('settings.subscription.cancel') }}" 
                       class="ajax-link btn btn-outline-danger"
                       data-method="POST"
                       data-confirm-type="danger"
                       data-confirm="Are you sure you want to cancel your subscription? You may lose access to active modules at the end of your billing cycle."
                       data-confirm-title="Cancel Subscription">
                        <i class="fas fa-times-circle me-2"></i> Cancel Subscription
                    </a>
                @endif
            </div>
        @endif

    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h5 class="mb-0 text-danger"><i class="fas fa-file-invoice me-2"></i>Pending Bills</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Invoice No.</th>
                        <th>Billing Period</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Generated Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No pending bills.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h5 class="mb-0 text-success"><i class="fas fa-check-circle me-2"></i>Paid Bills</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Invoice No.</th>
                        <th>Billing Period</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Generated Date</th>
                        <th>Paid Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No paid bills history found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
