<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_id',
        'transaction_id',
        'gateway',
        'amount',
        'currency',
        'status',
        'payload'
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'payload' => 'array'
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
