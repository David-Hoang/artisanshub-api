<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Craftsman extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'craftsman_job_id',
        'price',
        'available',
        'description',
        'cover'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(CraftsmanJob::class, 'craftsman_job_id');
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(CraftsmanGallery::class);
    }

    public function prestations()
    {
        return $this->hasMany(Prestation::class);
    }

    public function displayName() 
    {
        return $this->user->first_name. ' ' . $this->user->last_name;
    }
}
