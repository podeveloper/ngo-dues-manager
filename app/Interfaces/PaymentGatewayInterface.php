<?php

namespace App\Interfaces;

use App\Models\Invoice;
use App\Models\User;

interface PaymentGatewayInterface
{
    public function charge(User $user, Invoice $invoice, ?string $cardNumber = null): array;
}
