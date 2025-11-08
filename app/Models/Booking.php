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
    // Adjusted to match simplified bookings schema used in tests
    'reference',
    'total_amount',
    'status',
    'contact',
    'meta',
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