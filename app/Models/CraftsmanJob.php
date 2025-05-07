<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class CraftsmanJob extends Model
{
    protected $fillable = [
        'name',
        'img_path',
        'img_title',
        'description'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function craftsman(): HasMany
    {
        return $this->hasMany(Craftsman::class, 'craftsman_job_id');
    }
}
