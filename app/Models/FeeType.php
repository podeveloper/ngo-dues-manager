<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'amount',
        'currency',
        'is_recurring'
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'amount' => 'decimal:2'
    ];
}
