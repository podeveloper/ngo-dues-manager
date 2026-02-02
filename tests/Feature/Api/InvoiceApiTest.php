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

    public function test_user_cannot_see_other_users_invoices()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $invoice1 = Invoice::create([
            'user_id' => $user1->id,
            'reference_code' => 'USER1-INV',
            'total_amount' => 100,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $invoice2 = Invoice::create([
            'user_id' => $user2->id,
            'reference_code' => 'USER2-INV',
            'total_amount' => 200,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        Sanctum::actingAs($user1);

        $response = $this->getJson('/api/invoices');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.reference_code', 'USER1-INV')
            ->assertJsonMissing(['reference_code' => 'USER2-INV']);
    }

    public function test_empty_invoice_list_returns_correctly()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/invoices');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data')
            ->assertJson(['status' => 'success', 'data' => []]);
    }

    public function test_invoices_include_items_and_fee_types()
    {
        $user = User::factory()->create();
        $feeType = FeeType::where('slug', 'dues_official')->first();

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'reference_code' => 'WITH-ITEMS',
            'total_amount' => 200,
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $invoice->items()->create([
            'fee_type_id' => $feeType->id,
            'description' => 'Test Fee',
            'quantity' => 1,
            'unit_price' => 200,
            'amount' => 200
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/invoices');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['items' => ['*' => ['fee_type']]]
                ]
            ]);
    }
}
