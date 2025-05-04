<?php

namespace App\Enums;

enum OrderStatus:string
{
    case COMPLETE = "complete";
    case INPROGRESS = "in-progress";
    case PENDING = "pending";
    case CANCELLED = "cancelled";

    public function displayName(): string
    {
        return match ($this) {
            self::COMPLETE => "Terminé",
            self::INPROGRESS => "En cours",
            self::PENDING => "En attente",
            self::CANCELLED => "Annulé",
        };
    }
}
