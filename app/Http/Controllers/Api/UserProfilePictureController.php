<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\UserProfilePicture;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class UserProfilePictureController extends Controller
{
    //Add or update user profile picture
    public function profilePicture(Request $req)
    {
        try {
            $user = Auth::user();

            $req->validate([
                "profile_picture" => "nullable|image|max:3072|mimes:jpg,png,jpeg,webp",
                "img_title" => "nullable|string|max:255"
            ], $this->messages());

            $oldTitle = $user->profileImg->img_title ?? null;
            $oldPath = $user->profileImg->img_path ?? null;

            $newProfilePicture = UserProfilePicture::updateOrCreate(
                ['user_id' => $user->id],
                [
                    "img_title" => $req->img_title ?? $oldTitle,
                    "img_path" => $oldPath,
                ]);
            
            if($req->hasFile('profile_picture')) {

                if($oldPath){
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $req->profile_picture->store('/img/profile/' . $user->id, 'public');
                $newProfilePicture->img_path = $path;
                $newProfilePicture->save();
            }

            return response()->json([
                "message" => "Photo de profil enregistrée avec succès ! ",
            ], 201);

        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur est survenue lors de l’enregistrement de la photo de profil.",
                "err" => $e->getMessage()
            ], 500);
        }
    }

    protected function messages() : array {
        return [
            "profile_picture.image" => "Le fichier doit être une image.",
            "profile_picture.max" => "L'image ne peut pas dépasser 3 Mo.",
            "profile_picture.mimes" => "L'image doit être au format JPG, PNG, JPEG ou WEBP.",

            "img_title.string" => "Le nom doit être une chaîne de caractères.",
            "img_title.max" => "Le nom ne peut pas dépasser 255 caractères.",
        ];
    }
}
