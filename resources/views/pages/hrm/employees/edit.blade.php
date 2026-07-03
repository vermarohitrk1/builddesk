<form action="{{ route('employees.update', $employee->id) }}" method="POST" class="ajax-form">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="{{ $employee->user->name }}" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $employee->user->email }}" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Employee Code</label>
            <input type="text" name="employee_code" class="form-control" value="{{ $employee->employee_code }}" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Designation</label>
            <input type="text" name="designation" class="form-control" value="{{ $employee->designation }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Joining Date</label>
            <input type="date" name="joining_date" class="form-control" value="{{ $employee->joining_date }}">
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Monthly Salary</label>
        <input type="number" name="salary" class="form-control" step="0.01" value="{{ $employee->salary }}">
    </div>
    <div class="mb-3">
        <div class="form-check form-switch">
            <input type="hidden" name="auto_generate_salary" value="0">
            <input class="form-check-input" type="checkbox" name="auto_generate_salary" id="auto_generate_salary_edit" value="1"
                {{ $employee->auto_generate_salary ? 'checked' : '' }}>
            <label class="form-check-label" for="auto_generate_salary_edit">
                Auto Generate Salary
                <small class="text-muted d-block">Include this employee in monthly payroll auto-generation (Salary only).</small>
            </label>
        </div>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update Employee</button>
    </div>
</form>
