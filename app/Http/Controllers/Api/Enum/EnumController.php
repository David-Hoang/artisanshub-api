<?php

namespace App\Http\Controllers\Api\Enum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Region;


class EnumController extends Controller
{
    public function Regions () {
        return response()->json([
            "regions" => Region::cases()
        ]);
    }
}
