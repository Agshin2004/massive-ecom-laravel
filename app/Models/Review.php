<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'body',
        'product_id',
        'rating',
        'user_id'
    ];

    public function user()
    {
        return $this->belongTo(User::class);
    }
}
