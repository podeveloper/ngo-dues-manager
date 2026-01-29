<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\Gateways\IyzicoGateway;
use App\Services\Gateways\StripeGateway;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected function getGateway(string $provider): PaymentGatewayInterface
    {
        return match ($provider) {
            'iyzico' => new IyzicoGateway(),
            'stripe' => new StripeGateway(),
            default => throw new Exception("Unsupported payment provider: {$provider}")
        };
    }

    public function payInvoice(User $user, int $invoiceId, string $provider = 'stripe'): Payment
    {
        return DB::transaction(function () use ($user, $invoiceId, $provider) {

            $invoice = Invoice::where('id', $invoiceId)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($invoice->status === 'paid') {
                throw new Exception("This invoice has already been paid!");
            }

            $gateway = $this->getGateway($provider);
            $result = $gateway->charge($user, $invoice->total_amount, $invoice->currency);

            if (! $result['success']) {
                throw new Exception("Payment failed via {$provider}");
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'transaction_id' => $result['transaction_id'],
                'gateway' => $provider,
                'amount' => $invoice->total_amount,
                'currency' => $invoice->currency,
                'status' => 'success',
                'payload' => $result['payload']
            ]);

            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            return $payment;
        });
    }
}
