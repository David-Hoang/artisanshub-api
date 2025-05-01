<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfilePicture extends Model
{

    protected $table = 'users_profile_picture'; //Telling laravel to use the table users_profile_picture
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'img_path',
        'img_title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
