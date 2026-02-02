<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\TestCard;
use App\Models\User;
use App\Services\Gateways\IyzicoGateway;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IyzicoGatewayTest extends TestCase
{
    use RefreshDatabase;

    private IyzicoGateway $gateway;
    private User $user;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gateway = new IyzicoGateway();
        $this->user = User::factory()->create();
        $this->invoice = Invoice::create([
            'user_id' => $this->user->id,
            'reference_code' => 'TEST-INV-IYZICO-001',
            'total_amount' => 100.00,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now()->addDays(7),
        ]);
    }

    public function test_it_charges_successfully_with_valid_card()
    {
        $card = TestCard::create([
            'card_number' => '1111222233334444',
            'should_succeed' => true,
            'bank_name' => 'Test Bank',
            'scheme' => 'Visa',
            'type' => 'Credit'
        ]);

        $result = $this->gateway->charge($this->user, $this->invoice, $card->card_number);

        $this->assertTrue($result['success']);
        $this->assertStringStartsWith('iyzi_', $result['transaction_id']);
    }

    public function test_it_throws_exception_with_failing_card()
    {
        $card = TestCard::create([
            'card_number' => '5555666677778888',
            'should_succeed' => false,
            'error_message' => 'Card Stolen',
            'error_code' => '404'
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Iyzico Payment Failed: Card Stolen (Error Code: 404)');

        $this->gateway->charge($this->user, $this->invoice, $card->card_number);
    }

    public function test_it_uses_default_card_when_no_card_number_provided()
    {
        TestCard::create([
            'card_number' => '1234567890123456',
            'should_succeed' => true,
            'bank_name' => 'Default Bank',
            'scheme' => 'Visa',
            'type' => 'Credit'
        ]);

        $result = $this->gateway->charge($this->user, $this->invoice);

        $this->assertTrue($result['success']);
        $this->assertStringStartsWith('iyzi_', $result['transaction_id']);
    }

    public function test_it_throws_exception_for_undefined_card()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Simulation Error: Undefined Test Card');

        $this->gateway->charge($this->user, $this->invoice, '9999888877776666');
    }

    public function test_it_includes_card_details_in_payload()
    {
        $card = TestCard::create([
            'card_number' => '7777888899990000',
            'should_succeed' => true,
            'bank_name' => 'Premium Bank',
            'scheme' => 'Mastercard',
            'type' => 'Debit'
        ]);

        $result = $this->gateway->charge($this->user, $this->invoice, $card->card_number);

        $this->assertEquals('Premium Bank', $result['payload']['bank']);
        $this->assertEquals('Mastercard', $result['payload']['card_family']);
        $this->assertEquals('Debit', $result['payload']['card_type']);
        $this->assertEquals($this->invoice->reference_code, $result['payload']['invoice_reference']);
    }

    public function test_it_handles_different_error_codes()
    {
        $card = TestCard::create([
            'card_number' => '1111000011110000',
            'should_succeed' => false,
            'error_message' => 'Insufficient Funds',
            'error_code' => '51'
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Iyzico Payment Failed: Insufficient Funds (Error Code: 51)');

        $this->gateway->charge($this->user, $this->invoice, $card->card_number);
    }
}
