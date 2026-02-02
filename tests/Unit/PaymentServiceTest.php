<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\User;
use App\Models\TestCard;
use App\Services\Gateways\IyzicoGateway;
use App\Services\PaymentGatewayFactory;
use App\Services\PaymentService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Support\Fakes\StripeGatewayFake;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;
    private User $user;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bindPaymentGateways();
        $this->paymentService = app(PaymentService::class);

        $this->user = User::factory()->create();

        $this->invoice = Invoice::create([
            'user_id' => $this->user->id,
            'reference_code' => 'TEST-INV-001',
            'total_amount' => 100.00,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now()->addDays(7),
        ]);

        TestCard::create([
            'card_number' => '1234567890123456',
            'bank_name' => 'Test Bank',
            'scheme' => 'Visa',
            'type' => 'Credit',
            'should_succeed' => true,
        ]);
    }

    private function bindPaymentGateways(): void
    {
        $factory = new PaymentGatewayFactory();
        $factory->register('stripe', new StripeGatewayFake());
        $factory->register('iyzico', new IyzicoGateway());

        $this->app->instance(PaymentGatewayFactory::class, $factory);
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

        $this->invoice->refresh();
        $this->assertEquals('paid', $this->invoice->status);
        $this->assertNotNull($this->invoice->paid_at);
        $this->assertTrue($this->invoice->paid_at->isToday());
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

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invoice not found or does not belong to you.');
        $this->paymentService->payInvoice($otherUser, $this->invoice->id);
    }

    public function test_it_throws_exception_for_unsupported_gateway()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported payment provider: bitcoin');

        $this->paymentService->payInvoice($this->user, $this->invoice->id, 'bitcoin');
    }

    public function test_it_rolls_back_transaction_on_gateway_failure()
    {
        // Setup a failing card for Iyzico simulation
        $failingCard = TestCard::create([
            'card_number' => '9999999999999999',
            'bank_name' => 'Fail Bank',
            'scheme' => 'Visa',
            'type' => 'Credit',
            'should_succeed' => false,
            'error_message' => 'Insufficient Funds',
            'error_code' => '51'
        ]);

        try {
            $this->paymentService->payInvoice(
                $this->user,
                $this->invoice->id,
                'iyzico',
                $failingCard->card_number
            );
            $this->fail('Expected exception was not thrown.');
        } catch (Exception $e) {
            $this->assertStringContainsString('Insufficient Funds', $e->getMessage());
        }

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => 'pending'
        ]);

        $this->assertDatabaseMissing('payments', [
            'invoice_id' => $this->invoice->id
        ]);
    }
}
