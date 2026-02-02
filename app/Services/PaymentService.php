<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private readonly PaymentGatewayFactory $gatewayFactory
    ) {}

    public function payInvoice(User $user, int $invoiceId, string $gateway = 'stripe'): Payment
    {
        $paymentGateway = $this->gatewayFactory->make($gateway);

        return DB::transaction(function () use ($user, $invoiceId, $gateway, $paymentGateway) {

            $invoice = Invoice::where('id', $invoiceId)
                ->where('user_id', $user->id)
                ->first();

            if (! $invoice) {
                throw new Exception("Invoice not found or does not belong to you.");
            }

            if ($invoice->status === 'paid') {
                throw new Exception("This invoice has already been paid!");
            }

            $cardNumber = request()->input('card_number');
            $result = $paymentGateway->charge($user, $invoice->total_amount, $invoice->currency, $cardNumber);

            if (! $result['success']) {
                throw new Exception("Payment Failed: " . json_encode($result['payload']));
            }

            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            return Payment::create([
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'gateway' => $gateway,
                'transaction_id' => $result['transaction_id'],
                'amount' => $invoice->total_amount,
                'currency' => $invoice->currency,
                'status' => 'success',
                'payload' => json_encode($result['payload']),
            ]);
        });
    }
}
