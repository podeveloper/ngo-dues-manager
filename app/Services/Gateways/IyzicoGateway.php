<?php

namespace App\Services\Gateways;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\TestCard;
use App\Models\User;
use Illuminate\Support\Str;
use Exception;

class IyzicoGateway implements PaymentGatewayInterface
{
    public function charge(User $user, float $amount, string $currency, ?string $cardNumber = null): array
    {
        if ($cardNumber) {
            $cardNumber = preg_replace('/[^0-9]/', '', $cardNumber);
        }

        if (empty($cardNumber)) {
            $defaultCard = TestCard::where('should_succeed', true)->first();
            $cardNumber = $defaultCard ? $defaultCard->card_number : '0000';
        }

        $card = TestCard::where('card_number', $cardNumber)->first();

        if (! $card) {
            throw new Exception("Simulation Error: Undefined Test Card ($cardNumber)");
        }

        if ($card->should_succeed) {
            return [
                'success' => true,
                'transaction_id' => 'iyzi_' . Str::random(10),
                'payload' => [
                    'provider' => 'iyzico',
                    'status' => 'success',
                    'bank' => $card->bank_name,
                    'card_family' => $card->scheme,
                    'card_type' => $card->type,
                    'system_time' => now()->toDateTimeString()
                ]
            ];
        } else {
            throw new Exception("Iyzico Payment Failed: {$card->error_message} (Error Code: {$card->error_code})");
        }
    }
}
