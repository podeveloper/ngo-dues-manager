<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Illuminate\Console\Command;

class GenerateMonthlyDues extends Command
{
    protected $signature = 'dues:generate';

    protected $description = 'Generate monthly membership dues for all users';

    public function handle(BillingService $billingService): void
    {
        $this->info('Starting billing process...');

        $count = $billingService->generateMonthlyDues();

        $this->info("Successfully generated invoices for {$count} users.");
    }
}
