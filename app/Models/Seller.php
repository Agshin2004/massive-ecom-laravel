<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;

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
}
