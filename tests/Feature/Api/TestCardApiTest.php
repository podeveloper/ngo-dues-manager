<?php

namespace Tests\Feature\Api;

use App\Models\TestCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TestCardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_test_cards()
    {
        $response = $this->getJson('/api/test-cards');

        $response->assertStatus(401);
    }

    public function test_user_can_list_test_cards()
    {
        TestCard::create([
            'card_number' => '1111222233334444',
            'bank_name' => 'Test Bank',
            'should_succeed' => true,
        ]);

        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/test-cards');

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'data'])
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.card_number', '1111222233334444');
    }
}
