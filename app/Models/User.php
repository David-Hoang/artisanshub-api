<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use App\Enums\Region;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'role',
        'password',
        'phone',
        'city',
        'region',
        'zipcode'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'role' => Role::class,
            'region' => Region::class
        ];
    }

    public function craftsman(): HasOne
    {
        return $this->hasOne(Craftsman::class);
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function profileImg(): HasOne
    {
        return $this->hasOne(UserProfilePicture::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // return user role
    public function getRole(): HasOne
    {
        return $this->hasOne(Client::class) ? $this->hasOne(Client::class) : $this->hasOne(Craftsman::class);
    }
}
