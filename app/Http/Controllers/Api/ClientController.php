<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    public function clientInfos(Request $req)
    {
        try {
            $user = Auth::user();

            $req->validate([
                "street_number" => "required|integer|min:0",
                "street_name" => "required|string|max:255",
                "complement" => "nullable|string|max:255",
            ], $this->messages());

            $client = Client::updateOrCreate(
                ["user_id" => $user->id],
                [
                    "street_name" => $req->street_name,
                    "street_number" => $req->street_number,
                    "complement" => $req->complement,
                ]
            );

            return response()->json(
                [
                    "message" => "Les informations ont bien été sauvegardés.",
                ],
                $client->wasRecentlyCreated ? 201 : 200 //throw 201 code if new client else 200);
            ); 
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

    protected function messages()
    {
        return [
            'street_number.required' => 'Le numéro de rue est requis.',
            'street_number.numeric' => 'Le numéro de rue doit être un nombre.',
            'street_number.min' => 'Le numéro de rue ne peut pas être négatif.',

            'street_name.required' => 'Le nom de la rue est requis.',
            'street_name.string' => 'Le nom de la rue doit être une chaîne de caractères.',
            'street_name.max' => 'Le nom de la rue ne peut pas dépasser 255 caractères.',

            'complement.string' => 'Le complément d\'adresse doit être une chaîne de caractères.',
            'complement.max' => 'Le complément d\'adresse ne peut pas dépasser 255 caractères.',
        ];
    }
}
