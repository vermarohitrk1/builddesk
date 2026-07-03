@extends('layouts.main')

@section('content')
<div class="mb-4">
    <nav class="breadcrumb-container" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item text-muted">HRM</li>
            <li class="breadcrumb-item active text-primary" aria-current="page">Attendance</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <h1>Attendance Management</h1>
    </div>
</div>

<div class="card border shadow-sm mb-4">
    <div class="card-header bg-white border-bottom pt-3 pb-0">
        <ul class="nav nav-tabs border-bottom-0" id="attendanceTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="take-attendance-tab" data-bs-toggle="tab" data-bs-target="#take-attendance" type="button" role="tab">
                    <i class="fas fa-calendar-check me-1"></i> Take Attendance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="attendance-records-tab" data-bs-toggle="tab" data-bs-target="#attendance-records" type="button" role="tab">
                    <i class="fas fa-table me-1"></i> Attendance Records
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content" id="attendanceTabsContent">
            
            {{-- Tab 1: Take Attendance --}}
            <div class="tab-pane fade show active" id="take-attendance" role="tabpanel">
                <div class="row mb-4 align-items-end g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Attendance Date <span class="text-danger">*</span></label>
                        <input type="date" id="attendance-date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-auto">
                        <a id="load-employees-btn" class="btn btn-primary ajax-link" href="{{ route('attendance.daily.get') }}" data-method="POST" data-data='@json(["date" => date("Y-m-d")])'>
                            <i class="fas fa-search me-1"></i> Load Employees
                        </a>
                    </div>
                </div>
                
                <hr>

                <form id="daily-attendance-form" class="ajax-form" action="{{ route('attendance.daily.store') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="attendance_date" id="form-attendance-date" value="{{ date('Y-m-d') }}">
                    
                    <div id="daily-attendance-container" class="table-responsive">
                        <!-- Loaded via AJAX -->
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success" id="save-attendance-btn">
                            <i class="fas fa-save me-1"></i> Save Attendance
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tab 2: Attendance Records (Matrix) --}}
            <div class="tab-pane fade" id="attendance-records" role="tabpanel">
                <form id="matrix-filter-form" class="ajax-form row g-3 mb-4" action="{{ route('attendance.matrix.get') }}" method="POST">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Month</label>
                        <select id="matrix-month" name="month" class="form-select form-select-sm">
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Year</label>
                        <select id="matrix-year" name="year" class="form-select form-select-sm">
                            @foreach(range(date('Y'), 2024) as $y)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Employee (Optional)</label>
                        <select id="matrix-employee" name="employee_id" class="form-select form-select-sm">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div id="matrix-container" class="table-responsive">
                    <!-- Matrix will be loaded via AJAX on tab click or filter change -->
                     <div class="text-center p-4"><i class="fas fa-circle-notch fa-spin fa-2x text-primary"></i><p class="mt-2">Loading matrix...</p></div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).on('change', '#attendance-date', function () {
        const date = $(this).val();
        $('#load-employees-btn').attr('data-data', JSON.stringify({
            date: date
        }));
        $('#form-attendance-date').val(date);
    });
    
    // Lazy load the matrix when tab is clicked the first time
    $('button[data-bs-target="#attendance-records"]').on('shown.bs.tab', function (e) {
        loadMatrix(); 
    });

    // Reload matrix when filters change
    $('#matrix-month, #matrix-year, #matrix-employee').on('change', function() {
        loadMatrix();
    });

    function loadMatrix() {
        document.getElementById('matrix-filter-form').requestSubmit();
    }
</script>
@endpush
@endsection
