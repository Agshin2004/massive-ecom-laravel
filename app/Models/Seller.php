<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use App\Enums\SellerStatus;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'slug',
        'status',
        'logo_url',
        'rating'
    ];

    public function isApproved()
    {
        return $this->status === SellerStatus::Approved->value;
    }

    protected function storeName()
    {
        return Attribute::make(
            set: fn (string $value) => ucfirst($value)
        );
    }

    public static function booted()
    {
        static::creating(function ($instance) {
            $instance->slug = Str::slug($instance->store_name);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
