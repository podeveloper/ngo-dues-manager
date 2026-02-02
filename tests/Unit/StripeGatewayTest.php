<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\User;
use Tests\Support\Stubs\StripeGatewayFailureStub;
use Tests\Support\Stubs\StripeGatewayStub;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeGatewayTest extends TestCase
{
    use RefreshDatabase;

    private $gateway;
    private User $user;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->invoice = Invoice::create([
            'user_id' => $this->user->id,
            'reference_code' => 'TEST-INV-001',
            'total_amount' => 100.00,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now()->addDays(7),
        ]);
    }

    public function test_it_charges_successfully()
    {
        $this->gateway = new StripeGatewayStub();
        $result = $this->gateway->charge($this->user, $this->invoice);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('transaction_id', $result);
        $this->assertArrayHasKey('payload', $result);
        $this->assertIsArray($result['payload']);
        $this->assertEquals('ch_test_123', $result['transaction_id']);
    }

    public function test_it_handles_failure_gracefully()
    {
        $this->gateway = new StripeGatewayFailureStub();
        $result = $this->gateway->charge($this->user, $this->invoice);

        $this->assertFalse($result['success']);
        $this->assertNull($result['transaction_id']);
        $this->assertArrayHasKey('error', $result['payload']);
        $this->assertStringContainsString('No API key provided', $result['payload']['error']);
    }

    public function test_it_lowercases_currency()
    {
        $this->gateway = new StripeGatewayStub();
        $invoice = Invoice::create([
            'user_id' => $this->user->id,
            'reference_code' => 'TEST-INV-UPPERCASE',
            'total_amount' => 100.00,
            'currency' => 'TRY', // Uppercase
            'status' => 'pending',
            'due_date' => now()->addDays(7),
        ]);

        $result = $this->gateway->charge($this->user, $invoice);

        $this->assertTrue($result['success']);
        $this->assertEquals('try', $result['payload']['currency']);
    }
}
