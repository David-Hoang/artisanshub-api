<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Message;
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
            $sender = Auth::user();

            if($sender->id === $receiver->id){
                return response()->json([
                        "message" => "Vous ne pouvez pas vous envoyer un message à vous même."
                    ], 403);
            }else{
                $req->validate([
                    "content" => "required|string|max:65535",
                ], $this->messages());
                
                Message::create([
                    "sender_id" =>  $sender->id,
                    "receiver_id" =>  $receiver->id,
                    "content" => $req->content
                ]);
                
                return response()->json(["message" => "Le message a bien été envoyé."], 201);
            }

        } catch (ModelNotFoundException $e) {
            // Throw this if receiver id doesn't exist
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

    public function allConversations()
    {
        try {
            $user = Auth::user();
            return response()->json($user->conversations(), 200);
            
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération des conversations."
            ], 500);
        }
    }

    protected function messages(): array
    {
        return [
            'content.required' => 'Le contenu du message est requis.',
            'content.string' => 'Le contenu doit être une chaîne de caractères.',
            'content.max' => 'Le contenu ne peut pas dépasser 65535 caractères.',
        ];
    }
}
