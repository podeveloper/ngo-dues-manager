<?php

namespace App\Interfaces;

use App\Models\User;

interface PaymentGatewayInterface
{
    public function charge(User $user, float $amount, string $currency): array;
}
