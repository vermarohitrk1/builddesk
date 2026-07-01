<form action="{{ route('payrolls.generate') }}" method="POST" class="ajax-form">
    @csrf
    <div class="row mb-3">
        <div class="col-12">
            <label class="form-label fw-bold">Select Employees <span class="text-danger">*</span></label>
            <div class="border rounded p-2" style="max-height: 200px; overflow-y:auto;">
                @foreach($employees as $emp)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" id="emp-{{ $emp->id }}">
                        <label class="form-check-label" for="emp-{{ $emp->id }}">
                            {{ $emp->user->name }}
                            <span class="text-muted small">({{ $emp->employee_code }})</span>
                            <span class="text-success small ms-1">₹{{ number_format($emp->salary, 2) }}</span>
                            @if($emp->auto_generate_salary)
                                <span class="badge bg-info text-dark ms-1" style="font-size:0.65rem">Auto</span>
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="mt-1">
                <button type="button" id="select-all-emps" class="btn btn-link btn-sm p-0">Select All</button> |
                <button type="button" id="deselect-all-emps" class="btn btn-link btn-sm p-0">Deselect All</button>
            </div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Payroll Type <span class="text-danger">*</span></label>
            <select name="payroll_type" class="form-select">
                @foreach(\App\Models\Payroll::TYPES as $type)
                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Month <span class="text-danger">*</span></label>
            <select name="payroll_month" class="form-select">
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == date('m') ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Year <span class="text-danger">*</span></label>
            <select name="payroll_year" class="form-select">
                @foreach(range(date('Y'), 2024) as $y)
                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-bold">Payroll Date <span class="text-danger">*</span></label>
            <input type="date" name="payroll_date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Remarks</label>
            <input type="text" name="remarks" class="form-control" placeholder="Optional...">
        </div>
    </div>
    <div class="text-end mt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i> Generate Payroll</button>
    </div>
</form>

<script>
document.getElementById('select-all-emps')?.addEventListener('click', function () {
    document.querySelectorAll('input[name="employee_ids[]"]').forEach(cb => cb.checked = true);
});
document.getElementById('deselect-all-emps')?.addEventListener('click', function () {
    document.querySelectorAll('input[name="employee_ids[]"]').forEach(cb => cb.checked = false);
});
</script>
