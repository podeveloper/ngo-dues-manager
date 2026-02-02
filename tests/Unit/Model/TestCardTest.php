<?php

namespace Tests\Unit\Model;

use App\Models\TestCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_test_card()
    {
        $card = TestCard::create([
            'card_number' => '1234567890123456',
            'bank_name' => 'Test Bank',
            'scheme' => 'Visa',
            'type' => 'Credit',
            'should_succeed' => true,
        ]);

        $this->assertDatabaseHas('test_cards', [
            'card_number' => '1234567890123456',
            'bank_name' => 'Test Bank',
            'scheme' => 'Visa',
        ]);
    }

    public function test_should_succeed_is_cast_to_boolean()
    {
        $card = TestCard::create([
            'card_number' => '1111222233334444',
            'bank_name' => 'Success Bank',
            'should_succeed' => 1,
        ]);

        $this->assertIsBool($card->should_succeed);
        $this->assertTrue($card->should_succeed);
    }

    public function test_can_create_failing_card_with_error_details()
    {
        $card = TestCard::create([
            'card_number' => '9999888877776666',
            'bank_name' => 'Fail Bank',
            'scheme' => 'Mastercard',
            'type' => 'Debit',
            'should_succeed' => false,
            'error_code' => '51',
            'error_message' => 'Insufficient Funds',
        ]);

        $this->assertDatabaseHas('test_cards', [
            'card_number' => '9999888877776666',
            'should_succeed' => false,
            'error_code' => '51',
            'error_message' => 'Insufficient Funds',
        ]);
    }

    public function test_can_create_card_with_different_schemes()
    {
        TestCard::create(['card_number' => '1111', 'scheme' => 'Visa', 'should_succeed' => true]);
        TestCard::create(['card_number' => '2222', 'scheme' => 'Mastercard', 'should_succeed' => true]);
        TestCard::create(['card_number' => '3333', 'scheme' => 'Troy', 'should_succeed' => true]);

        $this->assertDatabaseHas('test_cards', ['scheme' => 'Visa']);
        $this->assertDatabaseHas('test_cards', ['scheme' => 'Mastercard']);
        $this->assertDatabaseHas('test_cards', ['scheme' => 'Troy']);
    }

    public function test_can_create_card_with_different_types()
    {
        TestCard::create(['card_number' => '4444', 'type' => 'Credit', 'should_succeed' => true]);
        TestCard::create(['card_number' => '5555', 'type' => 'Debit', 'should_succeed' => true]);

        $this->assertDatabaseHas('test_cards', ['type' => 'Credit']);
        $this->assertDatabaseHas('test_cards', ['type' => 'Debit']);
    }

    public function test_nullable_error_fields_for_successful_cards()
    {
        $card = TestCard::create([
            'card_number' => '6666777788889999',
            'bank_name' => 'Success Bank',
            'should_succeed' => true,
        ]);

        $this->assertNull($card->error_code);
        $this->assertNull($card->error_message);
    }
}
