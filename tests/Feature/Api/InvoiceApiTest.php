<?php

namespace Tests\Feature\Api;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        FeeType::create(['name' => 'Official Dues', 'slug' => 'dues_official', 'amount' => 200, 'is_recurring' => true]);
    }

    public function test_unauthenticated_users_cannot_access_invoices()
    {
        $response = $this->getJson('/api/invoices');
        $response->assertStatus(401);
    }

    public function test_user_can_list_own_invoices()
    {
        $user = User::factory()->create();
        $invoice = Invoice::create([
            'user_id' => $user->id,
            'reference_code' => 'TEST-INV-001',
            'total_amount' => 200,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/invoices');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.reference_code', 'TEST-INV-001');
    }
}
