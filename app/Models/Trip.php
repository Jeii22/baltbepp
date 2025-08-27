<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin',
        'destination',
        'departure_time',
        'arrival_time',
        'capacity',
        'price',
    ];
}
