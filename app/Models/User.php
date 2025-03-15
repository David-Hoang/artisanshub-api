<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

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


    protected function casts(): array{
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}


