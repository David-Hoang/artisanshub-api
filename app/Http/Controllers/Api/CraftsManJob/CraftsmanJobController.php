<?php

namespace App\Http\Controllers\Api\CraftsManJob;

use App\Http\Controllers\Controller;
use App\Models\CraftsmanJob;
use Illuminate\Http\Request;

class CraftsmanJobController extends Controller
{
    public function jobs()
    {
        try {
            return response()->json([
                "jobs" => CraftsmanJob::all()->pluck('name')
            ]);
        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération des métiers.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}