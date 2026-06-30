<form action="{{ route('organisations.update', $organisation->id) }}" method="POST" class="ajax-form">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label class="form-label">Organisation Name</label>
        <input type="text" name="name" class="form-control" value="{{ $organisation->name }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Subscription Plan</label>
        <select name="subscription_plan_id" class="form-select" required>
            @foreach(\App\Models\SubscriptionPlan::where('is_active', true)->get() as $plan)
                <option value="{{ $plan->id }}" {{ $organisation->subscription_plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="active_status" class="form-select" required>
            <option value="active" {{ $organisation->active_status == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $organisation->active_status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="suspended" {{ $organisation->active_status == 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Organisation</button>
    </div>
</form>
