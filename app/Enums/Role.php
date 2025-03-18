<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = "admin";
    case CLIENT = "client";
    case CRAFTSMAN = "craftsman";

    public function displayName() {
        return match ($this) {
            self :: ADMIN => 'Administrateur',
            self :: CLIENT => 'Administrateur',
            self :: CRAFTSMAN => 'Artisan',
        };
    }
}
