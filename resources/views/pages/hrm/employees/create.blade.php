<form action="{{ route('employees.store') }}" method="POST" class="ajax-form">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Employee Code</label>
            <input type="text" name="employee_code" class="form-control" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Designation</label>
            <input type="text" name="designation" class="form-control">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Joining Date</label>
            <input type="date" name="joining_date" class="form-control">
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Monthly Salary</label>
        <input type="number" name="salary" class="form-control" step="0.01">
    </div>
    <div class="mb-3">
        <div class="form-check form-switch">
            <input type="hidden" name="auto_generate_salary" value="0">
            <input class="form-check-input" type="checkbox" name="auto_generate_salary" id="auto_generate_salary_create" value="1">
            <label class="form-check-label" for="auto_generate_salary_create">
                Auto Generate Salary
                <small class="text-muted d-block">Include this employee in monthly payroll auto-generation (Salary only).</small>
            </label>
        </div>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Register Employee</button>
    </div>
</form>
