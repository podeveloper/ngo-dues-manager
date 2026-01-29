<?php

namespace App\Services\Gateways;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\User;
use Illuminate\Support\Str;

class StripeGateway implements PaymentGatewayInterface
{
    public function charge(User $user, float $amount, string $currency): array
    {
        return [
            'success' => true,
            'transaction_id' => 'ch_stripe_' . Str::random(10),
            'payload' => ['provider' => 'stripe', 'status' => 'succeeded']
        ];
    }
}
