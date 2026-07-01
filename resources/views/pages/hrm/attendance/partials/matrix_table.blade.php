@if($employees->isEmpty())
    <div class="alert alert-info">No employees match this filter criteria.</div>
@else
    <table class="table table-bordered table-sm align-middle text-center mb-0 mt-3" style="font-size: 0.85rem;">
        <thead class="table-light">
            <tr>
                <th class="text-start sticky-left" style="min-width: 150px;">Employee</th>
                @for($d = 1; $d <= $daysInMonth; $d++)
                    <th style="min-width: 35px;">{{ $d }}</th>
                @endfor
                <th class="bg-success text-white" title="Total Present">P</th>
                <th class="bg-warning text-dark" title="Total Half Day">H</th>
                <th class="bg-primary text-white" title="Total Leave">L</th>
                <th class="bg-danger text-white" title="Total Absent">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $emp)
                @php
                    $empMatrix = $matrix[$emp->id] ?? [];
                    $empSummary = $summary[$emp->id] ?? ['present' => 0, 'half_day' => 0, 'leave' => 0, 'absent' => 0];
                @endphp
                <tr>
                    <td class="text-start fw-bold sticky-left text-nowrap">{{ $emp->user->name }}</td>
                    
                    {{-- Loop through every day of the month --}}
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $status = $empMatrix[$d] ?? null;
                            $str = '-';
                            $class = 'text-muted';
                            if ($status == 'present') { $str = 'P'; $class = 'text-success fw-bold'; }
                            elseif ($status == 'half_day') { $str = 'H'; $class = 'text-warning fw-bold'; }
                            elseif ($status == 'leave') { $str = 'L'; $class = 'text-primary fw-bold'; }
                            elseif ($status == 'absent') { $str = 'A'; $class = 'text-danger fw-bold'; }
                        @endphp
                        <td class="{{ $class }}">{{ $str }}</td>
                    @endfor
                    
                    {{-- Render Totals --}}
                    <td class="fw-bold text-success bg-light">{{ $empSummary['present'] }}</td>
                    <td class="fw-bold text-warning bg-light">{{ $empSummary['half_day'] }}</td>
                    <td class="fw-bold text-primary bg-light">{{ $empSummary['leave'] }}</td>
                    <td class="fw-bold text-danger bg-light">{{ $empSummary['absent'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<style>
/* Optional styling to keep employee name visible on x-scroll */
.sticky-left {
    position: sticky;
    left: 0;
    background-color: #fff;
    z-index: 1;
    border-right: 2px solid #dee2e6;
}
.table-light .sticky-left {
    background-color: #f8f9fa;
}
</style>
