<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Organisation;
use App\Models\User;
use App\Models\SubscriptionPlan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a default subscription plan
        $plan = SubscriptionPlan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'price' => 0,
            'is_active' => true,
        ]);

        // 2. Create the first organisation
        $org = Organisation::create([
            'name' => 'uPvc Chandigarh',
            'subscription_plan_id' => $plan->id,
            'active_status' => 'active',
        ]);

        // 3. Create Super Admin (no organisation_id if they are global, or linked to a system org)
        User::create([
            'name' => 'Super Admin',
            'email' => 'sadmin@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'super_admin',
        ]);

        // 4. Create Organisation Admin
        User::create([
            'organisation_id' => $org->id,
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'org_admin',
        ]);
    }
}
