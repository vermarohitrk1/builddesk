<div class="card">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card bg-white shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">Organisation Logo</h5>
                </div>
                <div class="card-body py-4">
                    <div class="text-center mb-4">
                        @if(auth()->user()->organisation->logo)
                            <img src="{{ asset('storage/' . auth()->user()->organisation->logo) }}" id="logo-preview" class="img-fluid border p-2 rounded" style="max-height: 150px;">
                        @else
                            <div class="bg-light border rounded d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                <span class="text-muted"><i class="fas fa-image fa-2x"></i><br>No Logo</span>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('settings.logo.update') }}" method="POST" class="ajax-form" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload New Logo</label>
                            <input type="file" name="logo" class="form-control" accept="image/*" required>
                            <small class="text-muted d-block mt-1">Recommended: Square or horizontal logo, max 2MB.</small>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Update Logo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card bg-white shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">Organisation Info</h5>
                </div>
                <div class="card-body py-4">
                    <div class="mb-3">
                        <label class="text-muted small fw-bold">Name</label>
                        <p class="mb-0 fs-5">{{ auth()->user()->organisation->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-bold">Email</label>
                        <p class="mb-0">{{ auth()->user()->organisation->email ?? 'Not set' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small fw-bold">Subscription Plan</label>
                        <div><span class="badge bg-info text-dark px-3 py-2">{{ auth()->user()->organisation->subscriptionPlan->name ?? 'N/A' }}</span></div>
                    </div>
                    <hr>
                    <div class="text-end">
                        <button class="btn btn-outline-secondary disabled" title="Global information synchronization is upcoming">Edit Details <i class="fas fa-clock ms-1"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
