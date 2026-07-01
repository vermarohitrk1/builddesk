<form action="{{ route('payrolls.generate') }}" method="POST" class="ajax-form">
    @csrf
    <div class="row g-3">
        <div class="col-md-7">
            <label class="form-label fw-bold">Select Employee <span class="text-danger">*</span></label>
            <select name="employee_id" id="generate_employee_id" class="form-select" required>
                <option value="">-- Select Employee --</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}">
                        {{ $emp->user->name }} ({{ $emp->employee_code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label fw-bold">Amount <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" name="amount" id="generate_amount" class="form-control" step="0.01" min="0" required>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold">Payroll Type <span class="text-danger">*</span></label>
            <select name="payroll_type" class="form-select" required>
                @foreach(\App\Models\Payroll::TYPES as $type)
                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Month <span class="text-danger">*</span></label>
            <select name="payroll_month" class="form-select" required>
                @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == date('m') ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">Year <span class="text-danger">*</span></label>
            <select name="payroll_year" class="form-select" required>
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
    $('#generate_employee_id').on('change', function() {
        var salary = $(this).find(':selected').data('salary');
        if(salary !== undefined) {
            $('#generate_amount').val(salary);
        } else {
            $('#generate_amount').val('');
        }
    });
</script>
