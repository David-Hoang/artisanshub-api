<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

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
    
    public function prestations()
    {
        return $this->hasMany(Prestation::class);
    }

    protected $appends = ['full_address'];

    public function getFullAddressAttribute()
    {
        $address = $this->street_number . ' ' . $this->street_name;
        
        if ($this->complement) {
            $address .= ', ' . $this->complement;
        }
        
        return $address;
    }
}
