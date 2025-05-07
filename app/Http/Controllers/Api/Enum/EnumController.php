<?php

namespace App\Http\Controllers\Api\Enum;

use App\Http\Controllers\Controller;
use App\Enums\Region;


class EnumController extends Controller
{
    public function Regions () {
        try {
            return response()->json([
                "regions" => Region::cases()
            ]);
        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération des régions."
            ], 500);
        }
    }
}
