@php
    $attendanceKeyed = isset($attendances) ? $attendances : collect();
@endphp

@if($employees->isEmpty())
    <div class="alert alert-warning">No active employees found to mark attendance.</div>
@else
    <table class="table table-bordered table-striped align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th width="30%">Employee</th>
                <th width="30%">Attendance Status <span class="text-danger">*</span></th>
                <th width="40%">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $emp)
                @php
                    // Get existing status if exists, else default to 'present'
                    $existing = $attendanceKeyed->get($emp->id);
                    $status = $existing ? $existing->status : 'present';
                    $remarks = $existing ? $existing->remarks : '';
                @endphp
                <tr>
                    <td class="fw-bold">
                        {{ $emp->user->name }}
                        <div class="small text-muted">{{ $emp->employee_code }}</div>
                    </td>
                    <td>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="attendance[{{ $emp->id }}][status]" id="status_p_{{ $emp->id }}" value="present" {{ $status == 'present' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success btn-sm" for="status_p_{{ $emp->id }}">Present</label>

                            <input type="radio" class="btn-check" name="attendance[{{ $emp->id }}][status]" id="status_h_{{ $emp->id }}" value="half_day" {{ $status == 'half_day' ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning btn-sm" for="status_h_{{ $emp->id }}">Half Day</label>

                            <input type="radio" class="btn-check" name="attendance[{{ $emp->id }}][status]" id="status_l_{{ $emp->id }}" value="leave" {{ $status == 'leave' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary btn-sm" for="status_l_{{ $emp->id }}">Leave</label>

                            <input type="radio" class="btn-check" name="attendance[{{ $emp->id }}][status]" id="status_a_{{ $emp->id }}" value="absent" {{ $status == 'absent' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger btn-sm" for="status_a_{{ $emp->id }}">Absent</label>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="attendance[{{ $emp->id }}][remarks]" class="form-control form-control-sm" value="{{ $remarks }}" placeholder="Optional log...">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
