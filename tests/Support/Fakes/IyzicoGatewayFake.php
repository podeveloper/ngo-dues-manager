<?php

namespace Tests\Support\Fakes;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Str;

class IyzicoGatewayFake implements PaymentGatewayInterface
{
    public function charge(User $user, Invoice $invoice, ?string $cardNumber = null): array
    {
        return [
            'success' => true,
            'transaction_id' => 'iyzi_' . Str::random(10),
            'payload' => [
                'provider' => 'iyzico',
                'status' => 'success',
                'amount' => $invoice->total_amount,
                'currency' => $invoice->currency,
            ],
        ];
    }
}
