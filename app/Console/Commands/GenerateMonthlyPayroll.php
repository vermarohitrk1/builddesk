<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyPayroll extends Command
{
    protected $signature = 'payroll:generate-monthly {--month= : Month (1-12)} {--year= : Year}';

    protected $description = 'Generate monthly salary payroll records for eligible employees (auto_generate_salary = 1)';

    public function handle()
    {
        $now   = Carbon::now();
        $month = (int) ($this->option('month') ?? $now->month);
        $year  = (int) ($this->option('year') ?? $now->year);

        $this->info("Generating payroll for {$month}/{$year}...");

        // Get all active employees with auto_generate_salary enabled
        $employees = Employee::where('auto_generate_salary', true)
            ->with('user')
            ->get();

        $generated = 0;
        $skipped   = 0;

        foreach ($employees as $employee) {
            // Skip if payroll already exists for this month
            $exists = Payroll::where('organisation_id', $employee->organisation_id)
                ->where('employee_id', $employee->id)
                ->where('payroll_type', 'salary')
                ->where('payroll_month', $month)
                ->where('payroll_year', $year)
                ->exists();

            if ($exists) {
                $skipped++;
                $this->line("  Skipped: {$employee->user->name} (already exists)");
                continue;
            }

            Payroll::create([
                'organisation_id' => $employee->organisation_id,
                'employee_id'     => $employee->id,
                'payroll_type'    => 'salary',
                'payroll_month'   => $month,
                'payroll_year'    => $year,
                'payroll_date'    => Carbon::create($year, $month, 1)->endOfMonth()->toDateString(),
                'amount'          => $employee->salary,
                'status'          => 'generated',
                'created_by'      => null,
            ]);

            $generated++;
            $this->line("  Generated: {$employee->user->name} — ₹{$employee->salary}");
        }

        $this->info("Done. Generated: {$generated}, Skipped: {$skipped}.");
        return Command::SUCCESS;
    }
}
