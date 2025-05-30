<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
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
    // List of prestation depend role
    public function listPrestations(Request $req)
    {
        try {
            $user = $req->user();

            if ($user->role === Role::CLIENT) {

                $client = $user->client()->with([
                    'prestations.craftsman:id,user_id',
                    'prestations.craftsman.user:id,first_name,last_name'
                ])->first();
                $prestations = $client->prestations;

            } else if ($user->role === Role::CRAFTSMAN) {

                $craftsman = $user->craftsman()->with([
                    'prestations.client:id,user_id',
                    'prestations.client.user:id,first_name,last_name'
                ])->first();
                $prestations = $craftsman->prestations;
                
            }

            return response()->json($prestations, 200);

        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la récupération des prestations.",
            ], 500);
        }
    }

    // Both client and craftsman can show prestation details
    public function showPrestation(Request $req, $prestationId)
    {
        try {
            $user = $req->user();

            if ($user->role === Role::CLIENT) {
                $prestation = $user->client
                    ->prestations()
                    ->with([
                        'craftsman.job:id,name',
                        'craftsman.user:id,last_name,first_name,phone,email',
                        'craftsman.user.profileImg:user_id,img_path,img_title'
                    ])
                    ->findOrFail($prestationId);

            } else if ($user->role === Role::CRAFTSMAN) {
                $prestation = $user->craftsman
                    ->prestations()
                    ->with([
                        'client.user:id,last_name,first_name,phone,email',
                        'client.user.profileImg:user_id,img_path,img_title'
                    ])
                    ->findOrFail($prestationId);
            } else {
                return response()->json([
                    "message" => "Vous n'avez pas accès à cette prestation."
                ], 403);
            }

            return response()->json($prestation, 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Prestation non trouvée.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la demande de la prestation."
            ], 500);
        }
    }    

    // Client create new prestation
    public function clientNewPrestation(Request $req, $craftsmanId)
    {
        try {
            $craftsman = Craftsman::findOrFail($craftsmanId);
            $user = $req->user();

            $req->validate([
                "title" => "required|string|max:255",
                "description" => "required|string|max:65535",
            ], $this->messages());

            Prestation::create([
                "client_id" => $user->client->id ?? null,
                "craftsman_id" => $craftsman->id ?? null,
                "title" => $req->title,
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
            ], 500);
        }
    }

    // Craftsman can proppose a date and price(quote) to the client
    public function craftsmanQuotePrestation(Request $req, $prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $craftsman = $req->user()->craftsman;
            
            // Craftsman can edit if it's still await-craftsman and if the prestation belongs to him
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
                "message" => "Une erreur s'est produite lors de l'enregistrement des informations."
            ], 500);
        }
    }

    //Client can accept the quote of the craftsman
    public function clientAcceptPrestation(Request $req, $prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $client = $req->user()->client;

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
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de l'acceptation de la prestation."
            ], 500);
        }
    }

    public function craftsmanCompletePrestation(Request $req, $prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $craftsman = $req->user()->craftsman;

            // Craftsman close this prestation if the status is confirmed and if the prestation belongs to him
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
                "message" => "Une erreur s'est produite lors de l'acceptation de la prestation."
            ], 500);
        }
    }

    public function craftsmanRefusePrestation(Request $req, $prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $craftsman = $req->user()->craftsman;

            // Craftsman can refuse if prestation is await-craftsman and if the prestation belongs to him
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
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors du refus de la prestation."
            ], 500);
        }
    }

    public function clientRefusePrestation(Request $req, $prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $client = $req->user()->client;

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
                "message" => "Une erreur s'est produite lors du refus de la prestation."
            ], 500);
        }
    }

    protected function messages(): array
    {
        return [
        'price.required' => 'Le prix est requis.',
        'price.numeric' => 'Le prix doit être un nombre.',
        'price.between' => 'Le prix doit être compris entre 0 et 99 999 999,99.',
    
        'title.required' => 'Veuillez renseigner le titre de la demande.',
        'title.string' => 'Le titre doit être une chaîne de caractères.',
        'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',

        'description.string' => 'La description doit être une chaîne de caractères.',
        'description.required' => 'Veuillez formuler votre demande.',
        'description.max' => 'La description ne doit pas dépasser 65 535 caractères.',
    
        'date.required' => 'La date est requise.',
        'date.date' => 'La date doit être une date valide.',
        'date.after' => 'La date doit être ultérieure à l\'heure actuelle.',
        ];
    }
}
