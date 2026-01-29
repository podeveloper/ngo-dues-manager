<?php

namespace App\Services;

use App\Models\FeeType;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingService
{
    public function generateMonthlyDues(): int
    {

        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $this->createInvoiceForUser($user);
            $count++;
        }

        return $count;
    }

    public function createInvoiceForUser(User $user): Invoice
    {
        return DB::transaction(function () use ($user) {
            $now = Carbon::now();

            $invoice = Invoice::create([
                'user_id' => $user->id,
                'reference_code' => 'INV-' . $now->format('Ym') . '-' . Str::upper(Str::random(6)),
                'due_date' => $now->copy()->addDays(15),
                'currency' => 'TRY',
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            $feeSlug = $user->membership_type === 'official' ? 'dues_official' : 'dues_candidate';
            $duesFee = FeeType::where('slug', $feeSlug)->firstOrFail();

            $invoice->items()->create([
                'fee_type_id' => $duesFee->id,
                'description' => $duesFee->name,
                'quantity' => 1,
                'unit_price' => $duesFee->amount,
                'amount' => $duesFee->amount,
            ]);
            $totalAmount += $duesFee->amount;


            if ($user->has_radio) {
                $radioFee = FeeType::where('slug', 'radio_fee')->first();
                if ($radioFee) {
                    $invoice->items()->create([
                        'fee_type_id' => $radioFee->id,
                        'description' => $radioFee->name . ' (Monthly Usage)',
                        'quantity' => 1,
                        'unit_price' => $radioFee->amount,
                        'amount' => $radioFee->amount,
                    ]);
                    $totalAmount += $radioFee->amount;
                }
            }

            $invoice->update(['total_amount' => $totalAmount]);

            return $invoice;
        });
    }
}
