<?php

namespace App\Services\Gateways;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\User;
use Illuminate\Support\Str;

class IyzicoGateway implements PaymentGatewayInterface
{
    public function charge(User $user, float $amount, string $currency): array
    {
        return [
            'success' => true,
            'transaction_id' => 'iyzi_' . Str::random(10),
            'payload' => ['provider' => 'iyzico', 'status' => 'success']
        ];
    }
}
