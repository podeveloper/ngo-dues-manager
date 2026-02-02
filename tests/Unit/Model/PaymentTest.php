<?php

namespace Tests\Unit\Model;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_belongs_to_invoice()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'gateway' => 'stripe',
            'transaction_id' => 'tx_123',
            'amount' => 100,
            'currency' => 'TRY',
            'status' => 'success'
        ]);

        $this->assertInstanceOf(Invoice::class, $payment->invoice);
        $this->assertEquals($invoice->id, $payment->invoice->id);
    }

    public function test_payment_belongs_to_user()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'gateway' => 'stripe',
            'transaction_id' => 'tx_456',
            'amount' => 200,
            'currency' => 'TRY',
            'status' => 'success'
        ]);

        $this->assertInstanceOf(User::class, $payment->user);
        $this->assertEquals($user->id, $payment->user->id);
    }

    public function test_payment_casts_amount_as_decimal()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'gateway' => 'stripe',
            'transaction_id' => 'tx_789',
            'amount' => 150.50,
            'currency' => 'TRY',
            'status' => 'success'
        ]);

        $this->assertIsString($payment->amount);
        $this->assertEquals('150.50', $payment->amount);
    }

    public function test_payment_casts_payload_as_array()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $payload = ['transaction_id' => 'tx_999', 'status' => 'completed'];

        $payment = Payment::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'gateway' => 'stripe',
            'transaction_id' => 'tx_999',
            'amount' => 100,
            'currency' => 'TRY',
            'status' => 'success',
            'payload' => $payload
        ]);

        $this->assertIsArray($payment->payload);
        $this->assertEquals($payload, $payment->payload);

        $retrieved = Payment::find($payment->id);
        $this->assertIsArray($retrieved->payload);
        $this->assertEquals($payload, $retrieved->payload);
    }

    public function test_can_create_payment_with_all_fields()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'gateway' => 'iyzico',
            'transaction_id' => 'iyzi_abc123',
            'amount' => 250.75,
            'currency' => 'TRY',
            'status' => 'success',
            'payload' => json_encode(['bank' => 'Test Bank'])
        ]);

        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'iyzi_abc123',
            'gateway' => 'iyzico',
            'amount' => 250.75,
            'status' => 'success'
        ]);
    }
}
