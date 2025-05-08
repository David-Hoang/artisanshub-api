<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MessageController extends Controller
{
    public function sendMessage(Request $req, $receiverId)
    {   
        try{

            $receiver = User::findOrFail($receiverId);
            $sender = Auth::user()->role;
            



        } catch (ModelNotFoundException $e) {
            // Throw this if craftsman id doesn't exist
            return response()->json([
                'message' => "Le destinataire est introuvable.",
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de l'envoi du message."
            ], 500);
        }
    }
}
