<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;

class User extends Authenticatable implements MustVerifyEmail 
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function likes()
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'buyer_id');
    }

    public function purchasedItems()
    {
        return $this->belongsToMany(Item::class, 'purchases', 'buyer_id', 'item_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
