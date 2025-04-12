<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    public function craftsmanJob()
    {
        return $this->belongsTo(CraftsmanJob::class);
    }
}
