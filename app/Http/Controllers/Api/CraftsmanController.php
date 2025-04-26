<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CraftsmanController extends Controller
{
    public function craftsmanInfos(Request $req) {
        
        try {
            $user = Auth::user();
            $req->validate([
                'price' => "nullable|numeric|between:0,99999999.99",
                'description' => "nullable|string|max:65535",
                'available' => "required|boolean",
                'craftsman_job_id' => "required|exists:craftsman_jobs,id"
            ],$this->messages());

            // Insert data into craftsman info
            $user->jobInfos()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'price' => $req->price,
                'description' => $req->description,
                'available' => $req->available,
                'craftsman_job_id' => $req->craftsman_job_id,
            ]);

            return response()->json([
                "message" => "Les informations ont bien été sauvegardés.",
            ], 201);
        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de l'enregistrement des informations.",
            ], 500);
        }
    }

    protected function messages(): array {
        return [
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.between' => 'Le prix doit être compris entre 0 et 99 999 999.99.',
    
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne doit pas dépasser 65 535 caractères.',
    
            'available.required' => 'La disponibilité est requise.',
            'available.boolean' => 'Le champ disponibilité doit être vrai ou faux.',
    
            'craftsman_job_id.required' => 'Le métier de l’artisan est requis.',
            'craftsman_job_id.exists' => 'Le métier sélectionné est invalide.',
        ];
    }
}
