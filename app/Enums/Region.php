<?php

namespace App\Enums;

enum Region:string
{
    case AUVERGNE_RHONE_ALPES = "Auvergne-Rhône-Alpes";
    case BOURGOGNE_FRANCHE_COMTE = "Bourgogne-Franche-Comté";
    case BRETAGNE = "Bretagne";
    case CENTRE_VAL_DE_LOIRE = "Centre-Val de Loire";
    case CORSE = "Corse";
    case GRAND_EST = "Grand Est";
    case HAUTS_DE_FRANCE = "Hauts-de-France";
    case ILE_DE_FRANCE = "Île-de-France";
    case NORMANDIE = "Normandie";
    case NOUVELLE_AQUITAINE = "Nouvelle-Aquitaine";
    case OCCITANIE = "Occitanie";
    case PAYS_DE_LA_LOIRE = "Pays de la Loire";
    case PROVENCE_ALPES_COTE_D_AZUR = "Provence-Alpes-Côte d'Azur";
    case GUADELOUPE = "Guadeloupe";
    case GUYANE = "Guyane";
    case MARTINIQUE = "Martinique";
    case MAYOTTE = "Mayotte";
    case LA_REUNION = "La Réunion";

    // public function displayName(): string
    // {
    //     return match ($this) {
    //         self::AUVERGNE_RHONE_ALPES => "Auvergne-Rhône-Alpes",
    //         self::BOURGOGNE_FRANCHE_COMTE => "Bourgogne-Franche-Comté",
    //         self::BRETAGNE => "Bretagne",
    //         self::CENTRE_VAL_DE_LOIRE => "Centre-Val de Loire",
    //         self::CORSE => "Corse",
    //         self::GRAND_EST => "Grand Est",
    //         self::HAUTS_DE_FRANCE => "Hauts-de-France",
    //         self::ILE_DE_FRANCE => "Île-de-France",
    //         self::NORMANDIE => "Normandie",
    //         self::NOUVELLE_AQUITAINE => "Nouvelle-Aquitaine",
    //         self::OCCITANIE => "Occitanie",
    //         self::PAYS_DE_LA_LOIRE => "Pays de la Loire",
    //         self::PROVENCE_ALPES_COTE_D_AZUR => "Provence-Alpes-Côte d'Azur",
    //         self::GUADELOUPE => "Guadeloupe",
    //         self::GUYANE => "Guyane",
    //         self::MARTINIQUE => "Martinique",
    //         self::MAYOTTE => "Mayotte",
    //         self::LA_REUNION => "La Réunion",
    //     };
    // }
}