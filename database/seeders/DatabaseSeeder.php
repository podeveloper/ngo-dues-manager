<?php

namespace Database\Seeders;

use App\Models\FeeType;
use App\Models\User;
use App\Services\BillingService; // <-- Servisimizi dahil ettik
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(BillingService $billingService): void
    {
        $fees = [
            [
                'name' => '2026 Membership Dues (Official)',
                'slug' => 'dues_official',
                'amount' => 200.00,
                'is_recurring' => true
            ],
            [
                'name' => '2026 Membership Dues (Candidate)',
                'slug' => 'dues_candidate',
                'amount' => 100.00,
                'is_recurring' => true
            ],
            [
                'name' => 'Radio Usage Fee',
                'slug' => 'radio_fee',
                'amount' => 50.00,
                'is_recurring' => true
            ],
            [
                'name' => 'NGO T-Shirt',
                'slug' => 'merch_tshirt',
                'amount' => 350.00,
                'is_recurring' => false
            ],
        ];

        foreach ($fees as $fee) {
            FeeType::create($fee);
        }

        User::factory()->create([
            'name' => 'Yasin Korkmaz',
            'email' => 'admin@example.com',
            'membership_type' => 'official',
            'has_radio' => true,
        ]);

        User::factory(5)->create(['membership_type' => 'official', 'has_radio' => false]);
        User::factory(5)->create(['membership_type' => 'candidate', 'has_radio' => false]);
        User::factory(3)->create(['membership_type' => 'official', 'has_radio' => true]);

        $this->call(TestCardSeeder::class);

        $this->command->info('Generating invoices via BillingService...');
        $count = $billingService->generateMonthlyDues();
        $this->command->info("Successfully generated invoices for {$count} users.");
    }
}
