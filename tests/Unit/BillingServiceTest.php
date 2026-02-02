<?php

namespace Tests\Unit;

use App\Models\FeeType;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BillingService $billingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->billingService = new BillingService();

        FeeType::create(['name' => 'Official Dues', 'slug' => 'dues_official', 'amount' => 200, 'is_recurring' => true]);
        FeeType::create(['name' => 'Candidate Dues', 'slug' => 'dues_candidate', 'amount' => 100, 'is_recurring' => true]);
        FeeType::create(['name' => 'Radio Fee', 'slug' => 'radio_fee', 'amount' => 50, 'is_recurring' => true]);
    }

    public function test_it_creates_correct_invoice_for_official_member()
    {
        $user = User::factory()->create([
            'membership_type' => 'official',
            'has_radio' => false
        ]);

        $invoice = $this->billingService->createInvoiceForUser($user);
        $invoice->load('items.feeType');

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
        $this->assertEquals(200, $invoice->total_amount);
        $this->assertCount(1, $invoice->items);
        $this->assertEquals('dues_official', $invoice->items->first()->feeType->slug);
    }

    public function test_it_creates_correct_invoice_for_candidate_member()
    {
        $user = User::factory()->create([
            'membership_type' => 'candidate',
            'has_radio' => false
        ]);

        $invoice = $this->billingService->createInvoiceForUser($user);
        $invoice->load('items.feeType');

        $this->assertEquals(100, $invoice->total_amount);
        $this->assertEquals('dues_candidate', $invoice->items->first()->feeType->slug);
    }

    public function test_it_adds_radio_fee_if_user_has_radio_assignment()
    {
        $user = User::factory()->create([
            'membership_type' => 'official',
            'has_radio' => true
        ]);

        $invoice = $this->billingService->createInvoiceForUser($user);
        $invoice->load('items.feeType');

        $this->assertEquals(250, $invoice->total_amount);
        $this->assertCount(2, $invoice->items);
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'unit_price' => 50
        ]);
    }

    public function test_it_generates_dues_for_all_users()
    {
        User::factory()->count(3)->create([
            'membership_type' => 'official'
        ]);

        $count = $this->billingService->generateMonthlyDues();

        $this->assertEquals(3, $count);
        $this->assertDatabaseCount('invoices', 3);
    }

    public function test_it_does_not_generate_dues_for_deleted_users()
    {
        $user = User::factory()->create([
            'membership_type' => 'official',
            'deleted_at' => now()
        ]);

        $count = $this->billingService->generateMonthlyDues();

        $this->assertEquals(0, $count);
        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_it_throws_exception_if_fee_type_is_missing()
    {
        FeeType::truncate();

        $user = User::factory()->create(['membership_type' => 'official']);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->billingService->createInvoiceForUser($user);
    }

    public function test_it_does_not_create_duplicate_invoice_for_same_month()
    {
        $user = User::factory()->create([
            'membership_type' => 'official',
            'has_radio' => false
        ]);

        $firstInvoice = $this->billingService->createInvoiceForUser($user);
        $secondInvoice = $this->billingService->createInvoiceForUser($user);
        $this->assertEquals($firstInvoice->id, $secondInvoice->id);
        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_it_creates_new_invoice_for_different_month()
    {
        $user = User::factory()->create([
            'membership_type' => 'official',
            'has_radio' => false
        ]);

        $firstInvoice = $this->billingService->createInvoiceForUser($user);
        $this->assertDatabaseCount('invoices', 1);

        $this->travel(1)->month();
        $secondInvoice = $this->billingService->createInvoiceForUser($user);

        $this->assertNotEquals($firstInvoice->id, $secondInvoice->id);
        $this->assertDatabaseCount('invoices', 2);
        $this->assertNotEquals($firstInvoice->reference_code, $secondInvoice->reference_code);
    }
}
