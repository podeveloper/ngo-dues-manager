<?php

namespace Tests\Feature\Api;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        FeeType::create(['name' => 'Official Dues', 'slug' => 'dues_official', 'amount' => 200, 'is_recurring' => true]);
        FeeType::create(['name' => 'Candidate Dues', 'slug' => 'dues_candidate', 'amount' => 100, 'is_recurring' => true]);
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

        $response = $this->postJson("/api/invoices/{$invoice->id}/pay");

        $response->assertStatus(400);
    }
}
