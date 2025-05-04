<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CraftsmanGallery extends Model
{

    protected $table = 'craftsman_gallery'; //Telling laravel to use the table craftsman_gallery

    protected $fillable = [
        'craftsman_id',
        'img_path',
        'img_title',
    ];

    public function craftsman(): BelongsTo
    {
        return $this->belongsTo(Craftsman::class, 'craftsman_id');
    }
}
