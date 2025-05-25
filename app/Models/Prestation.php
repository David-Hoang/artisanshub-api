<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestation extends Model
{
    protected $table = 'ordered_prestations'; //Telling laravel to use the table ordered_prestations

    protected $fillable = [
        'client_id',
        'craftsman_id',
        'price',
        'title',
        'description',
        'date',
        'state'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'state' => OrderStatus::class
        ];
    }

    public function craftsman(): BelongsTo
    {
        return $this->belongsTo(Craftsman::class, 'craftsman_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
