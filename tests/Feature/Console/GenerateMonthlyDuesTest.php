<?php

namespace Tests\Feature\Console;

use App\Models\FeeType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateMonthlyDuesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup necessary FeeTypes
        FeeType::create(['name' => 'Official Dues', 'slug' => 'dues_official', 'amount' => 200, 'is_recurring' => true]);
        FeeType::create(['name' => 'Candidate Dues', 'slug' => 'dues_candidate', 'amount' => 100, 'is_recurring' => true]);
    }

    public function test_console_command_generates_dues()
    {
        // Create 2 users
        User::factory()->count(2)->create(['membership_type' => 'official']);

        $this->artisan('dues:generate')
             ->expectsOutput('Starting billing process...')
             ->expectsOutput('Successfully generated invoices for 2 users.')
             ->assertExitCode(0);

        $this->assertDatabaseCount('invoices', 2);
    }
}
