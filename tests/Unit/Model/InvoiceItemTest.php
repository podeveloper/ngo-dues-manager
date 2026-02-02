<?php

namespace Tests\Unit\Model;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_item_belongs_to_invoice()
    {
        $invoice = Invoice::factory()->create();
        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'fee_type_id' => FeeType::create(['name' => 'F', 'slug' => 'f', 'amount' => 10])->id,
            'description' => 'Desc',
            'quantity' => 1,
            'unit_price' => 10,
            'amount' => 10
        ]);

        $this->assertInstanceOf(Invoice::class, $item->invoice);
        $this->assertEquals($invoice->id, $item->invoice->id);
    }

    public function test_invoice_item_belongs_to_fee_type()
    {
        $invoice = Invoice::factory()->create();
        $feeType = FeeType::create(['name' => 'Test Fee', 'slug' => 'test_fee', 'amount' => 50]);
        
        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'fee_type_id' => $feeType->id,
            'description' => 'Desc',
            'quantity' => 1,
            'unit_price' => 50,
            'amount' => 50
        ]);

        $this->assertInstanceOf(FeeType::class, $item->feeType);
        $this->assertEquals($feeType->id, $item->feeType->id);
    }
}
