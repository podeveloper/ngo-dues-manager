<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reference_code' => 'INV-' . Str::random(10),
            'total_amount' => $this->faker->numberBetween(100, 1000),
            'currency' => 'TRY',
            'status' => 'pending',
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'paid_at' => null,
        ];
    }
}
