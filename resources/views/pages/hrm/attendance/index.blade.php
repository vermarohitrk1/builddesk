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
                        <button type="button" id="load-employees-btn" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Load Employees
                        </button>
                    </div>
                </div>

                <hr>

                <form id="daily-attendance-form" style="display:none;">
                    @csrf
                    <input type="hidden" name="attendance_date" id="form-attendance-date">
                    
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
                <form id="matrix-filter-form" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Month</label>
                        <select id="matrix-month" class="form-select form-select-sm">
                            @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Year</label>
                        <select id="matrix-year" class="form-select form-select-sm">
                            @foreach(range(date('Y'), 2024) as $y)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Employee (Optional)</label>
                        <select id="matrix-employee" class="form-select form-select-sm">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div id="matrix-container" class="table-responsive">
                    <!-- Matrix will be loaded via AJAX on tab click or filter change -->
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    
    // --- TAB 1: Daily Attendance ---
    $('#load-employees-btn').on('click', function() {
        const date = $('#attendance-date').val();
        if(!date) {
            toastr.error('Please select an attendance date.');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Loading...');

        $.ajax({
            url: '{{ route('attendance.daily.get') }}',
            data: { date: date },
            success: function(res) {
                if(res.status === 'success') {
                    $('#form-attendance-date').val(date);
                    $('#daily-attendance-container').html(res.data.html);
                    $('#daily-attendance-form').slideDown();
                } else {
                    toastr.error(res.message);
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-search me-1"></i> Load Employees');
            }
        });
    });

    $('#daily-attendance-form').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#save-attendance-btn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
            url: '{{ route('attendance.daily.save') }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if(res.status === 'success') {
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message || 'Validation error');
                }
            },
            error: function(xhr) {
                const res = xhr.responseJSON;
                toastr.error(res?.message || 'Error occurred while saving attendance.');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Attendance');
            }
        });
    });


    // --- TAB 2: Matrix Records ---
    let matrixLoaded = false;
    
    // Lazy load the matrix when tab is clicked the first time
    $('button[data-bs-target="#attendance-records"]').on('shown.bs.tab', function (e) {
        if(!matrixLoaded) {
            loadMatrix();
            matrixLoaded = true;
        }
    });

    // Reload matrix when filters change
    $('#matrix-month, #matrix-year, #matrix-employee').on('change', function() {
        if ($('#attendance-records').hasClass('active')) {
            loadMatrix();
        } else {
            matrixLoaded = false; // Mark for reload next time tab is opened
        }
    });

    function loadMatrix() {
        const month = $('#matrix-month').val();
        const year = $('#matrix-year').val();
        const employeeId = $('#matrix-employee').val();
        
        $('#matrix-container').html('<div class="text-center p-4"><i class="fas fa-circle-notch fa-spin fa-2x text-primary"></i><p class="mt-2">Loading matrix...</p></div>');

        $.ajax({
            url: '{{ route('attendance.matrix.get') }}',
            data: { month: month, year: year, employee_id: employeeId },
            success: function(res) {
                if(res.status === 'success') {
                    $('#matrix-container').html(res.data.html);
                } else {
                    $('#matrix-container').html('<div class="alert alert-danger">Error loading matrix: ' + res.message + '</div>');
                }
            },
            error: function() {
                $('#matrix-container').html('<div class="alert alert-danger">Failed to connect to the server.</div>');
            }
        });
    }

});
</script>
@endpush
@endsection
