<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'first_name',
        'last_name',
        'type',
        'gender',
        'birthdate',
        'id_number',
        'fare',
        'seat',
        'extra',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'fare' => 'decimal:2',
        'extra' => 'array',
    ];

    /**
     * Get the booking that owns the passenger.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the full name of the passenger.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
