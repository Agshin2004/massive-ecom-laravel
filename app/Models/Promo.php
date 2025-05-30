<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'code',
        'discount_percent',
        'discount_amount',
        'valid_till',
        'usage_limit',
        'per_usage_limit'
    ];
}
