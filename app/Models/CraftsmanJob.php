<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CraftsmanJob extends Model
{
        /** @use HasFactory<\Database\Factories\UserFactory> */
        use HasFactory;

        protected $fillable = [
            'name'
        ];

        public function Crafsman () 
        {
            return $this->hasMany(CraftsMan::class);
        }
}
