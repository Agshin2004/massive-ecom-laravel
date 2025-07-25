<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Review;
use App\Models\Seller;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'username',
        'email',
        'role',
        'phone_number',
        'password',
    ];

    public $keyType = 'string';  // done since pk is used as uuid
    public $incrementing = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    // public static function booted()
    // {
    //     static::creating(function ($instance) {
    //         $instance->id = (string) Str::uuid();
    //     });
    // }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        // user will be identified by his id
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function seller()
    {
        return $this->hasOne(Seller::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin->value;
    }

    public function isSeller()
    {
        return $this->role === Role::Seller->value;
    }
}
