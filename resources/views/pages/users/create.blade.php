<form action="{{ route('users.store') }}" method="POST" class="ajax-form">
    @csrf
    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
            <option value="org_admin">Organisation Admin</option>
            <option value="employee">Employee</option>
            @if(Auth::user()->role === 'super_admin')
                <option value="super_admin">Super Admin</option>
            @endif
        </select>
    </div>

    @if(Auth::user()->role === 'super_admin')
    <div class="mb-3">
        <label class="form-label">Organisation</label>
        <select name="organisation_id" class="form-select select2" required>
            <option value="">Select Organisation</option>
            @foreach($organisations as $org)
                <option value="{{ $org->id }}">{{ $org->name }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create User</button>
    </div>
</form>
