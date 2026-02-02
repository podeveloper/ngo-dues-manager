<?php

namespace App\Services\Gateways;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\User;
use Exception;
use Stripe\Stripe;
use Stripe\Charge;

class StripeGateway implements PaymentGatewayInterface
{
    public function charge(User $user, float $amount, string $currency, ?string $cardNumber = null): array
    {

        Stripe::setApiKey(config('services.stripe.secret'));

        try {

            $charge = Charge::create([
                'amount' => $amount * 100,
                'currency' => strtolower($currency),
                'source' => 'tok_visa',
                'description' => "Payment for Invoice #{$user->id}",
                'metadata' => [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]
            ]);

            return [
                'success' => true,
                'transaction_id' => $charge->id,
                'payload' => $charge->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'transaction_id' => null,
                'payload' => ['error' => $e->getMessage()]
            ];
        }
    }
}
