<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id'
    ];

    public static function booted()
    {
        static::creating(function ($instance) {
            $instance->id = Str::uuid();
        });
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
