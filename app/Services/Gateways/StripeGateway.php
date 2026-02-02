<?php

namespace App\Services\Gateways;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Invoice;
use App\Models\User;
use Exception;
use Stripe\Stripe;
use Stripe\Charge;

class StripeGateway implements PaymentGatewayInterface
{
    public function charge(User $user, Invoice $invoice, ?string $cardNumber = null): array
    {

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $chargePayload = $this->createCharge([
                'amount' => $invoice->total_amount * 100,
                'currency' => strtolower($invoice->currency),
                'source' => 'tok_visa',
                'description' => "Payment for Invoice {$invoice->reference_code}",
                'metadata' => [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'invoice_id' => $invoice->id,
                    'invoice_reference' => $invoice->reference_code,
                ]
            ]);

            return [
                'success' => true,
                'transaction_id' => $chargePayload['id'] ?? null,
                'payload' => $chargePayload
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'transaction_id' => null,
                'payload' => ['error' => $e->getMessage()]
            ];
        }
    }

    protected function createCharge(array $payload): array
    {
        $charge = Charge::create($payload);

        return $charge->toArray();
    }
}
