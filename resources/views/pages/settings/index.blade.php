@extends('layouts.main')

@section('content')
<div class="mb-4">
    <h1>Settings</h1>
    <p class="text-muted">Manage your organisation profile and preferences.</p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0">Organisation Logo</h5>
            </div>
            <div class="card-body py-4">
                <div class="text-center mb-4">
                    @if($organisation->logo)
                        <img src="{{ asset('storage/' . $organisation->logo) }}" id="logo-preview" class="img-fluid border p-2" style="max-height: 150px;">
                    @else
                        <div class="bg-light border d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                            <span class="text-muted">No Logo</span>
                        </div>
                    @endif
                </div>

                <form action="{{ route('settings.logo.update') }}" method="POST" class="ajax-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Upload New Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*" required>
                        <small class="text-muted">Recommended: Square or horizontal logo, max 2MB.</small>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Update Logo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card bg-white">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0">Organisation Info</h5>
            </div>
            <div class="card-body py-4">
                <p><strong>Name:</strong> {{ $organisation->name }}</p>
                <p><strong>Email:</strong> {{ $organisation->email ?? 'Not set' }}</p>
                <p><strong>Plan:</strong> <span class="badge bg-info text-dark">{{ $organisation->subscriptionPlan->name ?? 'N/A' }}</span></p>
                <hr>
                <button class="btn btn-outline-secondary disabled">Edit Details (Coming Soon)</button>
            </div>
        </div>
    </div>
</div>
@endsection
