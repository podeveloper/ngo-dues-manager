<?php

namespace Tests\Unit\Model;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_belongs_to_user()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $invoice->user);
        $this->assertEquals($user->id, $invoice->user->id);
    }

    public function test_invoice_has_many_items()
    {
        $invoice = Invoice::factory()->create();
        $feeType = FeeType::create(['name' => 'Test Fee', 'slug' => 'test_fee', 'amount' => 100]);

        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'fee_type_id' => $feeType->id,
            'description' => 'Test Item',
            'quantity' => 1,
            'unit_price' => 100,
            'amount' => 100
        ]);

        $this->assertTrue($invoice->items->contains($item));
        $this->assertInstanceOf(InvoiceItem::class, $invoice->items->first());
    }

    public function test_invoice_has_one_successful_payment()
    {
        $invoice = Invoice::factory()->create();

        $payment = Payment::create([
            'user_id' => $invoice->user_id,
            'invoice_id' => $invoice->id,
            'gateway' => 'stripe',
            'transaction_id' => 'tx_123',
            'amount' => $invoice->total_amount,
            'currency' => 'TRY',
            'status' => 'success'
        ]);

        $this->assertTrue($invoice->payments->contains($payment));
    }
}
