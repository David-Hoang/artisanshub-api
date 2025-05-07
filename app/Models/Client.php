<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'street_name',
        'street_number',
        'complement',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
