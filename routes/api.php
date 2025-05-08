<?php

use App\Enums\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\MessageController;
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

    //craftsman and client can get details of a prestation
    Route::get('prestation/{prestation}', [PrestationController::class, 'showPrestation']);

    Route::post('/message/send/{receiverId}', [MessageController::class, 'sendMessage']);

    Route::middleware('isCraftsman')->group(function() {
        //Craftsman infos
        Route::post('/craftsman-infos', [CraftsmanController::class, 'craftsmanInfos']);
        Route::prefix('prestation')->group(function() {
            Route::patch('/{prestationId}/quote', [PrestationController::class, 'craftsmanQuotePrestation']);
            Route::patch('/{prestationId}/craftsman-refuse', [PrestationController::class, 'craftsmanRefusePrestation']);
            Route::patch('/{prestationId}/completed', [PrestationController::class, 'craftsmanCompletePrestation']);
        });
    });

    Route::middleware('isClient')->group(function() {
        //Client infos
        Route::post('/client-infos', [ClientController::class, 'clientInfos']);

        Route::prefix('prestation')->group(function() {
            Route::post('/{craftsmanId}', [PrestationController::class, 'clientNewPrestation']);
            Route::patch('/{prestationId}/accept', [PrestationController::class, 'clientAcceptPrestation']);
            Route::patch('/{prestationId}/client-refuse', [PrestationController::class, 'clientRefusePrestation']);
        });
    });

});