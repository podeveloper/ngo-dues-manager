<?php

namespace Tests\Unit\Model;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_invoices()
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'reference_code' => 'INV-001',
            'total_amount' => 100,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now()
        ]);

        $this->assertTrue($user->invoices->contains($invoice));
        $this->assertInstanceOf(Invoice::class, $user->invoices->first());
    }

    public function test_user_has_many_payments()
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'reference_code' => 'INV-002',
            'total_amount' => 100,
            'currency' => 'TRY',
            'status' => 'paid',
            'due_date' => now()
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'gateway' => 'stripe',
            'transaction_id' => 'tx_123',
            'amount' => 100,
            'currency' => 'TRY',
            'status' => 'success'
        ]);

        $this->assertTrue($user->payments->contains($payment));
        $this->assertInstanceOf(Payment::class, $user->payments->first());
    }
}
