<?php

namespace App\Http\Controllers\Api\CraftsManJob;

use App\Http\Controllers\Controller;
use App\Models\CraftsmanJob;
use Illuminate\Http\Request;

class CraftsmanJobController extends Controller
{
    public function jobs()
    {
        return CraftsmanJob::all()->pluck('name');
    }
}
