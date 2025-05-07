<?php

namespace App\Http\Controllers\Api;

use App\Models\Craftsman;
use App\Enums\OrderStatus;
use App\Models\Prestation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PrestationController extends Controller
{   
    // Client create new prestation
    public function clientNewPrestation(Request $req, $craftsmanId)
    {
        try {
            $craftsman = Craftsman::findOrFail($craftsmanId);
            $user = Auth::user();

            $req->validate([
                "description" => "required|string|max:65535",
            ], $this->messages());

            // format price before insertion, 2 numbers after dot,
            // $formatPrice = (float)number_format($req->price, 2, ".", "");

            Prestation::create([
                "client_id" => $user->client->id ?? null,
                "craftsman_id" => $craftsman->id ?? null,
                "description" => $req->description,
                "state" => OrderStatus::AWAITCRAFTSMAN
            ]);

            return response()->json(["message" => "Une nouvelle demande de prestation a été créée."], 201);
            
        } catch (ModelNotFoundException $e) {
            // Throw this if craftsman id doesn't exist
            return response()->json([
                'message' => "L'artisan spécifié est introuvable.",
            ], 404);

        } catch (ValidationException $e) {
            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la demande de la prestation.",
                "e" => $e->getMessage()
            ], 500);
        }
    }

    // Both client and craftsman can show prestation details
    public function showPrestation($prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $user = Auth::user();

            //Checking if prestation owned by craftsman or client
            if($prestation->client_id === $user->client?->id || $prestation->craftsman_id === $user->craftsman?->id){
                return response()->json($prestation, 200);
            }else{
                return response()->json(["message" => "Accès refusé : vous ne pouvez pas consulter cette prestation.", 403]);
            }

        } catch (ModelNotFoundException $e) {
            // Throw this if craftsman id doesn't exist
            return response()->json([
                'message' => 'Prestation non trouvée.',
            ], 404);
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la demande de la prestation.",
                "e" => $e->getMessage()
            ], 500);
        }
    }

    // Craftsman can proppose a date and price(quote) to the client
    public function craftsmanQuotePrestation(Request $req, $prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $craftsman = Auth::user()->craftsman;
            
            // Client can edit if it's still pending and if the prestation belongs to them
            if($prestation->craftsman_id === $craftsman->id && $prestation->state === OrderStatus::AWAITCRAFTSMAN ){
                $req->validate([
                    "price" => "required|numeric|between:0,99999999.99",
                    "date" => "required|date|after:now",
                ], $this->messages());

                $formatPrice = (float)number_format($req->price, 2, ".", "");

                $prestation->update([
                    "price" => $formatPrice,
                    "date" => $req->date,
                    "state" => OrderStatus::AWAITCLIENT
                ]);

                return response()->json(
                    ["message" => "Les informations de la prestation ont bien été mises à jour."],
                    200
                );

            }else{
                return response()->json(
                    ["message" => "Cette prestation ne vous est pas assignée ou son état ne permet pas de répondre."], 403
                );
            }
        } catch (ModelNotFoundException $e) {
            // Throw this if prestation id doesn't exist
            return response()->json([
                'message' => 'Prestation non trouvée.',
            ], 404);
        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de l'enregistrement des informations.",
                "e" => $e->getMessage()
            ], 500);
        }
    }

    //Client can accept the quote of the craftsman
    public function clientAcceptPrestation($prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $client = Auth::user()->client;

            // Client accepting if the status is await-client and if the prestation belongs to him
            if($prestation->client_id === $client->id && $prestation->state === OrderStatus::AWAITCLIENT ){
                $prestation->update([
                    "state" => OrderStatus::CONFIRMED
                ]);
                return response()->json(["message" => "La prestation a été acceptée."], 200);
            }else {
                return response()->json(["message" => "Cette prestation ne vous est pas assignée ou son état ne permet pas de répondre."], 403);
            }
        } catch (ModelNotFoundException $e) {
            // Throw this if prestation id doesn't exist
            return response()->json([
                'message' => 'Prestation non trouvée.',
            ], 404);
        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de l'acceptation de la prestation.",
                "e" => $e->getMessage()
            ], 500);
        }
    }

    public function craftsmanCompletePrestation($prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $craftsman = Auth::user()->craftsman;

            // Client accepting if the status is await-client and if the prestation belongs to him
            if($prestation->craftsman_id === $craftsman->id && $prestation->state === OrderStatus::CONFIRMED ){
                $prestation->update([
                    "state" => OrderStatus::COMPLETED
                ]);

                return response()->json(["message" => "La prestation est maintenant complétée."], 200);

            }else {
                return response()->json(["message" => "Cette prestation ne vous est pas assignée ou son état ne permet pas de répondre."], 403);

            }
        } catch (ModelNotFoundException $e) {
            // Throw this if prestation id doesn't exist
            return response()->json([
                'message' => 'Prestation non trouvée.',
            ], 404);
        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de l'acceptation de la prestation.",
                "e" => $e->getMessage()
            ], 500);
        }
    }


    public function craftsmanRefusePrestation($prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $craftsman = Auth::user()->craftsman;

            // Client accepting if the status is await-client and if the prestation belongs to him
            if($prestation->craftsman_id === $craftsman->id && $prestation->state === OrderStatus::AWAITCRAFTSMAN){
                $prestation->update([
                    "state" => OrderStatus::REFUSEDBYCRAFTSMAN
                ]);
                return response()->json(["message" => "La prestation a été refusée."], 200);
            }else {
                return response()->json(["message" => "Cette prestation ne vous est pas assignée ou son état ne permet pas de répondre."], 403);
            }
        } catch (ModelNotFoundException $e) {
            // Throw this if prestation id doesn't exist
            return response()->json([
                'message' => 'Prestation non trouvée.',
            ], 404);
        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors du refus de la prestation.",
                "e" => $e->getMessage()
            ], 500);
        }
    }

    public function clientRefusePrestation($prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $client = Auth::user()->client;

            // Client accepting if the status is await-client and if the prestation belongs to him
            if($prestation->client_id === $client->id && $prestation->state === OrderStatus::AWAITCLIENT ){
                $prestation->update([
                    "state" => OrderStatus::REFUSEDBYCLIENT
                ]);
                return response()->json(["message" => "La prestation a été refusée."], 200);
            }else {
                return response()->json(["message" => "Cette prestation ne vous est pas assignée ou son état ne permet pas de répondre."], 403);
            }
        } catch (ModelNotFoundException $e) {
            // Throw this if prestation id doesn't exist
            return response()->json([
                'message' => 'Prestation non trouvée.',
            ], 404);
        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors du refus de la prestation.",
                "e" => $e->getMessage()
            ], 500);
        }
    }


    //Allow client to edit prestation while prestation is still pending
    // public function clientEditPrestation(Request $req, $prestationId)
    // {
    //     try {
    //         $prestation = Prestation::findOrFail($prestationId);
    //         $user = Auth::user();

    //         // Client can edit if it's still pending and if the prestation belongs to them
    //         if($prestation->state === OrderStatus::PENDING && $prestation->client_id === $user->client->id ){
    //             $req->validate([
    //                 "price" => "required|numeric|between:0,99999999.99",
    //                 "description" => "nullable|string|max:65535",
    //                 "date" => "required|date|after:now",
    //             ], $this->messages());

    //             $formatPrice = (float)number_format($req->price, 2, ".", "");

    //             $prestation->update([
    //                 "price" => $formatPrice,
    //                 "description" => $req->description,
    //                 "date" => $req->date
    //             ]);

    //             return response()->json(
    //                 ["message" => "Les informations de la prestation ont bien été enregistrées."],
    //                 $prestation->wasRecentlyCreated ? 201 : 200
    //             );

    //         }else {
                
    //             $message = "Vous n'êtes pas autorisé à modifier cette prestation.";
                
    //             if($prestation->state !== OrderStatus::PENDING){
    //                 $message = "La prestation n'est pas modifiable.";
    //             }
                
    //             if($prestation->client_id !== $user->client->id){
    //                 $message = "Accès refusé : La prestation ne vous appartient pas.";
    //             }
                

    //             return response()->json([
    //                 "message" => $message,
    //             ], 403);
    //         }
    //     } catch (ModelNotFoundException $e) {
    //         // Throw this if prestation id doesn't exist
    //         return response()->json([
    //             'message' => 'Prestation non trouvée.',
    //         ], 404);
    //     } catch (ValidationException $e) {

    //         return response()->json([
    //             "errors" => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         //Throw internal server error
    //         return response()->json([
    //             "message" => "Une erreur s'est produite lors de l'enregistrement des informations.",
    //             "e" => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // Client can cancel a prestation
    // public function clientCancelPrestation($prestationId)
    // {
    //     try {
    //         $prestation = Prestation::findOrFail($prestationId);
    //         $user = Auth::user();

    //         // Client can edit if it's still pending and if the prestation belongs to them
    //         if($prestation->client_id === $user->client->id ){
    //             $prestation->update([
    //                 "state" => OrderStatus::CANCELLED
    //             ]);

    //             return response()->json(["message" => "La prestation a été annulé."], 200);

    //         }else {
                
    //             return response()->json([
    //                 "message" => "Accès refusé : La prestation ne vous appartient pas.",
    //             ], 403);
    //         }
    //     } catch (ModelNotFoundException $e) {
    //         // Throw this if prestation id doesn't exist
    //         return response()->json([
    //             'message' => 'Prestation non trouvée.',
    //         ], 404);
    //     } catch (ValidationException $e) {

    //         return response()->json([
    //             "errors" => $e->errors()
    //         ], 422);
    //     } catch (\Exception $e) {
    //         //Throw internal server error
    //         return response()->json([
    //             "message" => "Une erreur s'est produite lors de l'annulation de la prestation.",
    //             "e" => $e->getMessage()
    //         ], 500);
    //     }
    // }



    protected function messages(): array
    {
        return [
        'price.required' => 'Le prix est requis.',
        'price.numeric' => 'Le prix doit être un nombre.',
        'price.between' => 'Le prix doit être compris entre 0 et 99 999 999,99.',
    
        'description.string' => 'La description doit être une chaîne de caractères.',
        'description.max' => 'La description ne doit pas dépasser 65 535 caractères.',
    
        'date.required' => 'La date est requise.',
        'date.date' => 'La date doit être une date valide.',
        'date.after' => 'La date doit être ultérieure à l\'heure actuelle.',
        ];
    }
}
