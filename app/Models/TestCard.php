<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_number',
        'bank_name',
        'scheme',       // Visa, Mastercard, Troy
        'type',         // Credit, Debit
        'should_succeed',
        'error_code',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'should_succeed' => 'boolean',
    ];
}
