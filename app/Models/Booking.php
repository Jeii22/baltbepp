<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trip_id',
        'origin',
        'destination',
        'departure_time',
        'adult',
        'child',
        'infant',
        'pwd',
        'student',
        'full_name',
        'email',
        'phone',
        'status',
        'total_amount',
        'payment_method',
        'payment_reference',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}