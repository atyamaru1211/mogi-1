<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profiles';

    protected $fillable = [
        'user_id',
        'profile_image_path',
        'name',
        'postal_code',
        'address',
        'building',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
