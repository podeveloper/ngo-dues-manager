<?php

namespace Tests\Feature\Api;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\User;
use App\Services\PaymentGatewayFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Support\Fakes\IyzicoGatewayFake;
use Tests\Support\Fakes\StripeGatewayFake;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bindPaymentGateways();
        FeeType::create(['name' => 'Official Dues', 'slug' => 'dues_official', 'amount' => 200, 'is_recurring' => true]);
        FeeType::create(['name' => 'Candidate Dues', 'slug' => 'dues_candidate', 'amount' => 100, 'is_recurring' => true]);
    }

    private function bindPaymentGateways(): void
    {
        $factory = new PaymentGatewayFactory();
        $factory->register('stripe', new StripeGatewayFake());
        $factory->register('iyzico', new IyzicoGatewayFake());

        $this->app->instance(PaymentGatewayFactory::class, $factory);
    }

    public function test_user_can_pay_invoice_via_api()
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'reference_code' => 'TEST-PAY-API',
            'total_amount' => 100,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/invoices/{$invoice->id}/pay", [
            'gateway' => 'stripe'
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.status', 'success');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid'
        ]);
    }

    public function test_user_cannot_pay_others_invoice_via_api()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $invoice = Invoice::create([
            'user_id' => $user1->id,
            'reference_code' => 'TEST-SECURE',
            'total_amount' => 100,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        Sanctum::actingAs($user2);

        $response = $this->postJson("/api/invoices/{$invoice->id}/pay", [
            'gateway' => 'stripe'
        ]);

        $response->assertStatus(400);
    }

    public function test_api_validates_gateway_parameter()
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'reference_code' => 'TEST-VALIDATION',
            'total_amount' => 100,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/invoices/{$invoice->id}/pay", [
            'gateway' => 'bitcoin' // Sending an invalid gateway
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gateway']);
    }

    public function test_payment_fails_with_non_existent_invoice()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/invoices/99999/pay", [
            'gateway' => 'stripe'
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('status', 'error');
    }

    public function test_payment_fails_with_invalid_invoice_id()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/invoices/invalid/pay", [
            'gateway' => 'stripe'
        ]);

        $response->assertStatus(400);
    }

    public function test_payment_uses_default_gateway_when_not_specified()
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'reference_code' => 'DEFAULT-GATEWAY',
            'total_amount' => 100,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/invoices/{$invoice->id}/pay");

        $response->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'gateway' => 'stripe'
        ]);
    }

    public function test_payment_with_card_number_parameter()
    {
        $user = User::factory()->create();

        \App\Models\TestCard::create([
            'card_number' => '1234567890123456',
            'bank_name' => 'Test Bank',
            'scheme' => 'Visa',
            'type' => 'Credit',
            'should_succeed' => true,
        ]);

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'reference_code' => 'WITH-CARD',
            'total_amount' => 100,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/invoices/{$invoice->id}/pay", [
            'gateway' => 'iyzico',
            'card_number' => '1234567890123456'
        ]);

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_pay_invoice()
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'reference_code' => 'NO-AUTH',
            'total_amount' => 100,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $response = $this->postJson("/api/invoices/{$invoice->id}/pay", [
            'gateway' => 'stripe'
        ]);

        $response->assertStatus(401);
    }
}
