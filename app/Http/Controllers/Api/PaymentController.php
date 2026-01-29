<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function pay(Request $request, int $id)
    {
        $request->validate([
            'gateway' => 'nullable|in:stripe,iyzico'
        ]);

        try {
            // Servisi çağır ve ödemeyi yap
            $payment = $this->paymentService->payInvoice(
                $request->user(),
                $id,
                $request->input('gateway', 'stripe')
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Payment processed successfully',
                'data' => $payment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
