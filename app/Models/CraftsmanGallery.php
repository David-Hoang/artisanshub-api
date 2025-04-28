<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CraftsmanGallery extends Model
{

    protected $table = 'craftsman_gallery'; //Telling laravel to use the table craftsman_gallery

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
