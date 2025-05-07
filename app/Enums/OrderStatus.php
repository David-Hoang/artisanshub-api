<?php

namespace App\Enums;

enum OrderStatus:string
{
    case AWAITCRAFTSMAN = "await-craftsman";
    case AWAITCLIENT = "await-client";
    case REFUSEDBYCLIENT = "refused-by-client";
    case REFUSEDBYCRAFTSMAN = "refused-by-craftsman";
    case CONFIRMED = "confirmed";
    case COMPLETED = "completed";


    public function displayName(): string
    {
        return match ($this) {
            self::AWAITCRAFTSMAN => "En attente artisan",
            self::AWAITCLIENT => "En attente client",
            self::REFUSEDBYCLIENT => "Refusé par le client",
            self::REFUSEDBYCRAFTSMAN => "Refusé par l'artisan",
            self::CONFIRMED => "Confirmé",
            self::COMPLETED => "Terminé",
        };
    }
}
