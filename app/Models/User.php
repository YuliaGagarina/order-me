<?php

namespace App\Models;

use App\Enums\UserGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'second_name',
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
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be stored encrypted
     *
     * @var string[]
     */
    protected $encrypted = [
        'first_name',
        'second_name',
        'email',
    ];

    /**
     * @return string[]
     */
    public function getEncryptedAttributes(): array
    {
        return $this->encrypted;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'vendor_id', 'id');
    }
}
