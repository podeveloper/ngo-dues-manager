<?php

namespace Tests\Unit\Model;

use App\Models\FeeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_fee_type()
    {
        $feeType = FeeType::create([
            'name' => 'Annual Dues',
            'slug' => 'annual_dues',
            'amount' => 150.50,
            'is_recurring' => true,
        ]);

        $this->assertDatabaseHas('fee_types', [
            'slug' => 'annual_dues',
            'amount' => 150.50
        ]);
    }
}
