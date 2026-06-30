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

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Register Employee</button>
    </div>
</form>
