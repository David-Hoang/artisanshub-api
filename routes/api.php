<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CraftsmanController;
use App\Http\Controllers\Api\Enum\EnumController;
use App\Http\Controllers\Api\PrestationController;
use App\Http\Controllers\Api\CraftsmanJobController;
use App\Http\Controllers\Api\UserProfilePictureController;

// ------- Public -----------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/enums/regions', [EnumController::class, 'regions']);

Route::get('/jobs', [CraftsmanJobController::class, 'jobs']);

Route::get('/craftsmen', [CraftsmanController::class, 'listCraftsmen']);
Route::get('/craftsman/public/{craftsmanId}', [CraftsmanController::class, 'showCraftsmanPublic']);

Route::middleware('auth:sanctum')->group(function() {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::patch('/me/update', [AuthController::class, 'updateUserInfos']);
    Route::patch('/me/update-password', [AuthController::class, 'updateUserPassword']);

    //Upload user profile picture
    Route::post('/user-profile-picture', [UserProfilePictureController::class, 'profilePicture']);

    // ------- Prestations -----------
    //craftsman and client can get details of a prestation
    Route::get('prestation/{prestation}', [PrestationController::class, 'showPrestation']);
    // get list prestation
    Route::get('/prestations', [PrestationController::class, 'listPrestations']);


    Route::get('/craftsman/private/{craftsmanId}', [CraftsmanController::class, 'showCraftsmanPrivate']);

    // ------- Messages -----------
    Route::prefix('message')->group(function() {
        //Send message
        Route::post('/send/{receiverId}', [MessageController::class, 'sendMessage']);
        //Get list conversation
        Route::get('/all-conversations', [MessageController::class, 'allConversations']);
        //Get conversation with an user
        Route::get('/conversation/{userWithId}', [MessageController::class, 'conversationWith']);
    });

    // ------- Craftsman -----------
    Route::middleware('isCraftsman')->group(function() {
        //Craftsman infos
        Route::post('/craftsman-infos', [CraftsmanController::class, 'craftsmanInfos']);

        // Delete gallery
        Route::delete('/photo-gallery/{photoId}', [CraftsmanController::class, 'deletePhotoGallery']);

        // Set cover image
        Route::patch('/cover/{photoId}', [CraftsmanController::class, 'setCover']);

        Route::prefix('prestation')->group(function() {
            Route::patch('/{prestationId}/quote', [PrestationController::class, 'craftsmanQuotePrestation']);
            Route::patch('/{prestationId}/craftsman-refuse', [PrestationController::class, 'craftsmanRefusePrestation']);
            Route::patch('/{prestationId}/completed', [PrestationController::class, 'craftsmanCompletePrestation']);
        });
    });

    // ------- Client -----------
    Route::middleware('isClient')->group(function() {
        //Client infos
        Route::post('/client-infos', [ClientController::class, 'clientInfos']);

        Route::prefix('prestation')->group(function() {
            Route::post('/{craftsmanId}', [PrestationController::class, 'clientNewPrestation']);
            Route::patch('/{prestationId}/accept', [PrestationController::class, 'clientAcceptPrestation']);
            Route::patch('/{prestationId}/client-refuse', [PrestationController::class, 'clientRefusePrestation']);
        });
    });

    // ------- Admin -----------
    Route::middleware('isAdmin')->prefix('admin')->group(function() {
        //Jobs categories
        Route::get('/jobs', [CraftsmanJobController::class, 'adminJobs']);
        Route::get('/jobs/{id}', [CraftsmanJobController::class, 'singleJob']);
        Route::post('/jobs/new-job', [CraftsmanJobController::class, 'addJob']);
        Route::patch('/job/{craftsmanJobId}/update', [CraftsmanJobController::class, 'updateJob']);
        Route::delete('/job/{craftsmanJobId}', [CraftsmanJobController::class, 'deleteJob']);

        // Users
        Route::get('/all-users', [AuthController::class, 'allUsers']);
        Route::get('/user/{userId}', [AuthController::class, 'singleUser']);
        Route::delete('/user/{userId}', [AuthController::class, 'deleteUser']);
    });

});