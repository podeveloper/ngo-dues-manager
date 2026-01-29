<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\User;
use App\Services\PaymentService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;
    private User $user;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new PaymentService();

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

    public function test_it_processes_payment_successfully()
    {
        $payment = $this->paymentService->payInvoice($this->user, $this->invoice->id);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'invoice_id' => $this->invoice->id,
            'status' => 'success',
            'gateway' => 'stripe'
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function test_it_uses_correct_gateway_strategy()
    {
        $payment = $this->paymentService->payInvoice($this->user, $this->invoice->id, 'iyzico');

        $this->assertEquals('iyzico', $payment->gateway);

        $this->assertStringStartsWith('iyzi_', $payment->transaction_id);
    }

    public function test_it_prevents_double_payment_for_same_invoice()
    {
        $this->paymentService->payInvoice($this->user, $this->invoice->id);

        $this->invoice->refresh();
        $this->assertEquals('paid', $this->invoice->status);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('This invoice has already been paid!');
        $this->paymentService->payInvoice($this->user, $this->invoice->id);
    }

    public function test_user_cannot_pay_others_invoice()
    {
        $otherUser = User::factory()->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->paymentService->payInvoice($otherUser, $this->invoice->id);
    }
}
