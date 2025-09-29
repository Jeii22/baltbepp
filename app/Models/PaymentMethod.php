<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',        // gcash, paymaya
        'label',       // Display label e.g., "GCash Main Wallet"
        'account_name',
        'account_number', // phone number or ID
        'is_active',
        'qr_code_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}