<?php

namespace Tests\Support\Fakes;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Invoice;
use App\Models\User;

class StripeGatewayFake implements PaymentGatewayInterface
{
    public function charge(User $user, Invoice $invoice, ?string $cardNumber = null): array
    {
        return [
            'success' => true,
            'transaction_id' => 'ch_test_123',
            'payload' => [
                'provider' => 'stripe',
                'status' => 'success',
                'amount' => $invoice->total_amount,
                'currency' => strtolower($invoice->currency),
            ],
        ];
    }
}
