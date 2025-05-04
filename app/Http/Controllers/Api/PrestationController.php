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
    // Create new prestation from client
    public function newPrestation(Request $req, $craftsmanId)
    {
        try {
            $craftsman = Craftsman::findOrFail($craftsmanId);
            $user = Auth::user();

            $req->validate([
                "price" => "required|numeric|between:0,99999999.99",
                "description" => "nullable|string|max:65535",
                "date" => "required|date|after:now",
            ], $this->messages());

            // format price before insertion, 2 numbers after dot,
            $formatPrice = (float)number_format($req->price, 2, ".", "");

            Prestation::create([
                "client_id" => $user->client->id ?? null,
                "craftsman_id" => $craftsman->id ?? null,
                "price" => $formatPrice,
                "description" => $req->description ?? null,
                "date" => $req->date,
                "state" => OrderStatus::PENDING
            ]);

            return response()->json(["message" => "Une nouvelle prestation a été créer."], 201);
            
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
                "message" => "Une erreur s'est produite lors de l'enregistrement des informations.",
                "e" => $e->getMessage()
            ], 500);
        }
    }


    //Allow client to edit prestation while prestation is still pending
    public function clientEditPrestation(Request $req, $prestationId)
    {
        try {
            $prestation = Prestation::findOrFail($prestationId);
            $user = Auth::user();

            // Client can edit if it's still pending and if the prestation belongs to them
            if($prestation->state === OrderStatus::PENDING && $prestation->client_id === $user->client->id ){
                $req->validate([
                    "price" => "required|numeric|between:0,99999999.99",
                    "description" => "nullable|string|max:65535",
                    "date" => "required|date|after:now",
                ], $this->messages());

                $formatPrice = (float)number_format($req->price, 2, ".", "");

                $prestation->update([
                    "price" => $formatPrice,
                    "description" => $req->description,
                    "date" => $req->date
                ]);

                return response()->json(
                    ["message" => "Les informations de la prestation ont bien été enregistrées."],
                    $prestation->wasRecentlyCreated ? 201 : 200
                );

            }else {
                return response()->json([
                    "message" => "Vous n'êtes pas autorisé à modifier cette prestation.",
                ], 403);
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
