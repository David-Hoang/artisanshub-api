<?php

namespace App\Http\Controllers\Api;

use App\Models\Craftsman;
use Illuminate\Http\Request;
use App\Models\CraftsmanGallery;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CraftsmanController extends Controller
{
    //Add or update craftsman infos
    public function craftsmanInfos(Request $req)
    {
        try {
            $user = Auth::user();

            $req->validate([
                "price" => "nullable|numeric|between:0,99999999.99",
                "description" => "nullable|string|max:65535",
                "available" => "required|boolean",
                "craftsman_job_id" => "required|exists:craftsman_jobs,id",
                "gallery.*" => "nullable|image|max:3072|mimes:jpg,png,jpeg,webp"
            ], $this->messages());

            // Insert data into craftsman info
            $craftsman = Craftsman::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'price' => $req->price,
                    'description' => $req->description,
                    'available' => $req->available,
                    'craftsman_job_id' => $req->craftsman_job_id,
                ]
            );

            //Create folder for craftsman's gallery and insert new records for each image
            if ($req->hasFile('gallery')) {
                foreach ($req->gallery as $image) {
                    $path = $image->store('/img/gallery/' . $craftsman->id, 'public');
                    CraftsmanGallery::create([
                        "craftsman_id" => $craftsman->id,
                        "img_path" => $path,
                    ]);
                }
            }

            return response()->json(
                [
                    "message" => "Les informations ont bien été sauvegardés.",
                ],
                $craftsman->wasRecentlyCreated ? 201 : 200
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

    protected function messages(): array
    {
        return [
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.between' => 'Le prix doit être compris entre 0 et 99 999 999.99.',

            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne doit pas dépasser 65 535 caractères.',

            'available.required' => 'La disponibilité est requise.',
            'available.boolean' => 'Le champ disponibilité doit être vrai ou faux.',

            'craftsman_job_id.required' => 'Le métier de l’artisan est requis.',
            'craftsman_job_id.exists' => 'Le métier sélectionné est invalide.',

            'gallery.*.image' => 'Chaque fichier de la galerie doit être une image.',
            'gallery.*.max' => 'Chaque image de la galerie ne doit pas dépasser 3 Mo.',
            'gallery.*.mimes' => 'Chaque image doit être au format JPG, PNG, JPEG ou WEBP.',
        ];
    }
}
