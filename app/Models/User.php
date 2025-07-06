<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use App\Enums\Region;
use App\Models\Message;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        ->with([
            'craftsman:id,user_id,craftsman_job_id',
            'craftsman.job:id,name',
            'craftsman.user:id',
            'profileImg:user_id,img_path,img_title'
            ])->get();

        $uniqueUsersInfos = $uniqueUsersInfos->map(function($user) {

            // get all messages sent to $user
            $userSent = $this->sentMessages->where('receiver_id', $user->id);
            
            //get all message from $user
            $userReceived = $this->receivedMessages->where('sender_id', $user->id);
            
            //combine to get whole message from sender and receiver 
            $allMessages = $userSent->merge($userReceived);

            // get the last message for sender and receiver
            $latestMessage = $allMessages
                        ->sortByDesc('created_at')
                        ->first();
            
            $user->last_message = $latestMessage->only(['id', 'content', 'created_at', 'receiver_id', 'sender_id']);

            return $user;
        });
        return $uniqueUsersInfos;
    }

    public function conversationWith($userWithId)
    {
            // get all messages sent to $userWithId
            $userSent = $this->sentMessages()->where('receiver_id', $userWithId)->get();

            //get all message from $userWithId
            $userReceived = $this->receivedMessages()->where('sender_id', $userWithId)->get();

            $fullConversation = $userSent->merge($userReceived)->sortBy('created_at')->values();

            return $fullConversation;
    }
}
