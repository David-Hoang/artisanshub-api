<?php

use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CraftsmanController;
use App\Http\Controllers\Api\Enum\EnumController;
use App\Http\Controllers\Api\PrestationController;
use App\Http\Controllers\Api\CraftsmanJobController;
use App\Http\Controllers\Api\UserProfilePictureController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/hello', function() {
//     dd(Role::CRAFTSMAN);
//     // return Role::CRAFTSMAN;
// });


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/enums/regions', [EnumController::class, 'regions']);

//Jobs categories
Route::get('/jobs', [CraftsmanJobController::class, 'jobs']);
Route::get('/jobs/{id}', [CraftsmanJobController::class, 'singleJob']);
Route::post('/jobs/new-job', [CraftsmanJobController::class, 'addJob']);

Route::middleware('auth:sanctum')->group(function() {

    Route::post('/auth', [AuthController::class, 'checkAuth']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/all-users', [AuthController::class, 'allUsers']);

    //Upload user profile picture
    Route::post('/user-profile-picture', [UserProfilePictureController::class, 'profilePicture']);


        Route::middleware('isCraftsman')->group(function() {
            //Craftsman infos
            Route::post('/craftsman-infos', [CraftsmanController::class, 'craftsmanInfos']);
        });

        Route::middleware('isClient')->group(function() {
            //Client infos
            Route::post('/client-infos', [ClientController::class, 'clientInfos']);
            Route::post('/new-prestation/{craftsmanId}', [PrestationController::class, 'newPrestation']);
            Route::put('/prestation/edit/{prestationId}', [PrestationController::class, 'clientEditPrestation']);
        });

});