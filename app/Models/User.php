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
        'client', //Hide property to prevent double data in JSON
        'craftsman', //Hide property to prevent double data in JSON
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

    // Accessor, it append property profile when convert to JSON
    protected $appends = ['profile'];

    // Get profile of user (Craftsman or Client)
    public function getProfileAttribute()
    {
        return $this->profile();
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

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
    
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    // return user profile
    public function profile()
    {
        if ($this->client()->exists()) {
            return $this->client;
        }
    
        if ($this->craftsman()->exists()) {
            return $this->craftsman;
        }
    
        return null;
    }

    // Get all users the current user has conversations with
    public function conversations()
    {
        // Get the IDs of users this user has sent messages to
        $sentTo = $this->sentMessages->pluck('receiver_id')->unique();

        // Get the IDs of users who have sent messages to this user
        $receivedFrom = $this->receivedMessages->pluck('sender_id')->unique();

        // Merge both collections to get all user IDs in an array
        $uniqueUsers = $sentTo->merge($receivedFrom)->unique();

        // Retrieve all users who have exchanged at least one message with the current user in a single query
        $uniqueUsersInfos = User::whereIn('id', $uniqueUsers)
        ->select('id', 'first_name', 'last_name', 'email')
        ->get();

        return $uniqueUsersInfos;
    }
}
