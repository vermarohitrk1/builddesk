<form action="{{ route('organisations.store') }}" method="POST" class="ajax-form">
    @csrf
    <div class="mb-3">
        <label class="form-label">Organisation Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Subscription Plan</label>
        <select name="subscription_plan_id" class="form-select" required>
            @foreach(\App\Models\SubscriptionPlan::where('is_active', true)->get() as $plan)
                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="active_status" class="form-select" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save Organisation</button>
    </div>
</form>
