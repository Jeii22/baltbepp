<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * Fillable columns for both legacy and new booking schema variants.
     * We include payment_method & payment_reference which are required for PayMongo flows.
     */
    protected $fillable = [
        'user_id', 'trip_id',
        'origin', 'destination', 'departure_time',
        'adult', 'child', 'infant', 'pwd', 'student',
        'full_name', 'email', 'phone',
        'payment_method', 'payment_reference',
        'total_amount', 'status',
        // Alternative schema fields
        'reference', 'contact', 'meta',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'total_amount' => 'decimal:2',
        'contact' => 'array',
        'meta' => 'array',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }
}