<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    public $keyType = 'string';
    public $incrementing = false;

    public static function booted()
    {
        static::creating(function ($instance) {
            $instance->id = Str::uuid();
        });
    }
}
