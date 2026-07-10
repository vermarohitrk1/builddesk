<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            ['name' => 'Leads', 'slug' => 'leads', 'price' => 1000.00, 'parent' => null, 'is_core' => true],
            ['name' => 'Measurement', 'slug' => 'measurement', 'price' => 500.00, 'parent' => 'leads', 'is_core' => false],
            ['name' => 'Quotation', 'slug' => 'quotation', 'price' => 500.00, 'parent' => 'leads', 'is_core' => false],
            ['name' => 'Follow-up', 'slug' => 'followup', 'price' => 500.00, 'parent' => 'leads', 'is_core' => false],
            ['name' => 'Projects', 'slug' => 'projects', 'price' => 500.00, 'parent' => 'leads', 'is_core' => true],
            ['name' => 'Customers', 'slug' => 'customers', 'price' => 500.00, 'parent' => 'leads', 'is_core' => true],
            ['name' => 'Expense', 'slug' => 'expense', 'price' => 500.00, 'parent' => null, 'is_core' => false],
            ['name' => 'Suppliers', 'slug' => 'suppliers', 'price' => 500.00, 'parent' => 'expense', 'is_core' => false],
            ['name' => 'Employees Management', 'slug' => 'employees', 'price' => 500.00, 'parent' => null, 'is_core' => false],
            ['name' => 'Attendance', 'slug' => 'attendance', 'price' => 500.00, 'parent' => 'employees', 'is_core' => false],
            ['name' => 'Payroll', 'slug' => 'payroll', 'price' => 500.00, 'parent' => 'employees', 'is_core' => false],
        ];

        foreach ($modules as $module) {
            \App\Models\Module::updateOrCreate(
                ['slug' => $module['slug']],
                ['name' => $module['name'], 'price' => $module['price'], 'parent' => $module['parent']]
            );
        }
    }
}
