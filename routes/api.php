<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CraftsmanJobController;
use App\Http\Controllers\Api\Enum\EnumController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/hello', function() {
//     return 'Hello world';
// });


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/enums/regions', [EnumController::class, 'regions']);

Route::get('/jobs', [CraftsmanJobController::class, 'jobs']);
Route::get('/jobs/{id}', [CraftsmanJobController::class, 'singleJob']);
Route::post('/jobs/new-job', [CraftsmanJobController::class, 'addJob']);

Route::middleware('auth:sanctum')->group(function() {

    Route::post('/auth', [AuthController::class, 'checkAuth']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/all-users', [AuthController::class, 'allUsers']);
    

});
