<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CraftsmanGallery extends Model
{
    protected $fillable = [
        'craftsman_id',
        'img_path',
        'img_title',
    ];

    public function craftsman()
    {
        return $this->belongsTo(Craftsman::class, 'craftsman_id');
    }
}
