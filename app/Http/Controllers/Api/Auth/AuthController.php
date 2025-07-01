<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\Role;
use App\Models\User;
use App\Enums\Region;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\UserProfilePicture;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $req) {

        try {
            $inputsValidated = $req->validate([
                "first_name" => "required|string|max:255",
                "last_name" => "required|string|max:255",
                "email" => "required|email|unique:users,email",
                "username" => "nullable|string|max:255",
                "role" => ["required", Rule::in(Role::cases())],
                "password" => ["required", "confirmed", Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
                "password_confirmation" => "required",
                "phone" => "required|regex:/^(0\d{9})$/",
                "city" => "required|string|max:255",
                "region" => ["required", Rule::in(Region::cases())],
                "zipcode" => "required|regex:/^\d{5}$/"
            ], $this->messages());
            
            // Create new user
            $newUser = User::create($inputsValidated);

            // Init profile picture to null
            UserProfilePicture::create([
                "user_id" => $newUser->id,
                "img_path" => null,
                "img_title" => null
            ]);

            // Generate token
            $userToken = $newUser->createToken($newUser->id);

            // populate infos for client or craftsman
            if($newUser->role === Role::CLIENT){
                $newUser->load(['client', 'profileImg']);
            }else if($newUser->role === Role::CRAFTSMAN){
                $newUser->load(['craftsman', 'profileImg', 'craftsman.gallery']);
            }
            
            return response()->json([
                "message" => "Utilisateur créé avec succès !",
                "user" => $newUser,
                "token" => $userToken->plainTextToken,
            ], 201);
        } catch (ValidationException $e) {

            // Throw validate error
            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de l'inscription."
            ], 500);
        }

    }

    public function login(Request $req) {
        
        try {
            $req->validate([
                "email" => "required|email",
                "password" => "required"
            ], $this->messages());
            
            $user = User::where('email', $req->email)->first();

            if (!$user) 
                return response()->json([
                    "message" => "Les informations de connexion ne sont pas valides."
                ], 401);

            if(!Hash::check($req->password, $user->password))
                return response()->json([
                    "message" => "Les informations de connexion ne sont pas valides."
                ], 401);
            
            // Token generate
            $userToken = $user->createToken($user->id);
            
            // populate infos for client or craftsman
            if($user->role === Role::CLIENT){
                $user->load(['client', 'profileImg']);
            }else if($user->role === Role::CRAFTSMAN){
                $user->load(['craftsman', 'profileImg', 'craftsman.gallery']);
            }

            return response()->json([
                "message" => "Connexion réussie !",
                "user" => $user,
                "token" => $userToken->plainTextToken
            ], 200);

        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la tentative de connexion.",
            ], 500);
        }
    }
    
    public function logout(Request $req) {
        try {
            $user = $req->user();
            if(!$user) return response()->json(["message" => "Vous n'êtes pas authentifié."], 401);

            $user->tokens()->delete();
            return response()->json(["message" => "Déconnexion réussie."], 200);

        } catch (\Exception $e) {
            return response()->json(["message" => "Une erreur s'est produite lors de la tentative de déconnexion."], 500);
        }
    }

    public function me(Request $req) {
        try {
            $user = $req->user();

            if($user->role === Role::CLIENT){
                return response()->json($user->load(['client', 'profileImg']), 200);
            }else if($user->role === Role::CRAFTSMAN){
                return response()->json($user->load(['craftsman', 'profileImg', 'craftsman.gallery:id,craftsman_id,img_path']), 200);
            }else if ($user->role === Role::ADMIN) {
                return response()->json($user, 200);
            }
            
        } catch (\Exception $e) {
            return response()->json(["message" => "Une erreur s'est produite lors de la récupération des données."], 500);
        }
    }

    public function updateUserInfos(Request $req) {
        try {
            $user = $req->user();
            $validation = $req->validate([
                "first_name" => "required|string|max:255",
                "last_name" => "required|string|max:255",
                "username" => "nullable|string|max:255",
                "phone" => "required|regex:/^(0\d{9})$/",
                "city" => "required|string|max:255",
                "region" => ["required", Rule::in(Region::cases())],
                "zipcode" => "required|regex:/^\d{5}$/"
            ], $this->messages());

            $user->update($validation);

            return response()->json(["message" => "Les informations ont bien été mises à jour."], 200);
        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de le mise à jour des informations."
            ], 500);
        }
    }

    public function updateUserPassword (Request $req) {
        
        try {
            $user = $req->user();

            $req->validate([
                "password" => "required",
                "new_password" => ["required", "confirmed", Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()],
                "new_password_confirmation" => "required"
            ], $this->messages());

            if(!Hash::check($req->password, $user->password))
                return response()->json([
                    "message" => "Le mot de passe actuel est incorrect."
                ], 401);
            
            $user->update([
                "password" => $req->new_password
            ]);

            return response()->json(["message" => "Le mot de passe a été mis à jour avec succès."], 200);
        } catch (ValidationException $e) {

            return response()->json([
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            //Throw internal server error
            return response()->json([
                "message" => "Une erreur s'est produite lors de la mise à jour du mot de passe."
            ], 500);
        }
    }

    public function allUsers () {
        try {
            $users = User::all();
            if(count($users) < 1){
                return response()->json(['message' => 'Aucun utilisateur existant'], 404);
            }else{
                return response()->json(User::all(), 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de l'inscription."
            ], 500);
        }
    }

    protected function messages() : array {
        return [
            // Register
            "first_name.required" => "Veuillez renseigner votre prénom.",
            "first_name.string" => "Le prénom doit être une chaîne de caractère.",
            "first_name.max" => "Le prénom ne doit pas dépasser 255 caractères.",

            "last_name.required" => "Veuillez renseigner votre nom.",
            "last_name.string" => "Votre nom doit être une chaîne de caractère.",
            "last_name.max" => "Le nom ne doit pas dépasser 255 caractères.",

            "email.required" => "Veuillez renseigner votre adresse email.",
            "email.email" => "Veuillez rentrer une adresse email valide.",
            "email.unique" => "Oops ! L'adresse email saisie est déjà utilisé. Veuillez en choisir un autre.",

            "username.string" => "Votre pseudo doit être une chaîne de caractère.",
            "username.max" => "Votre pseudo ne doit pas dépasser les 255 caractères.",

            "role.required" => "Veuillez renseigner votre role.",
            "role.in" => "Le role sélectionné n'est pas valide.",

            "password.required" => "Veuillez renseigner un mot de passe.",
            "password.confirmed" => "Les mots de passe ne correspondent pas.",
            "password.min" => "Le mot de passe doit contenir au moins 8 caractères.",
            "password.letters" => "Le mot de passe doit contenir au moins une lettre.",
            "password.mixed" => "Le mot de passe doit contenir au moins une lettre majuscule et une lettre minuscule.",
            "password.numbers" => "Le mot de passe doit contenir au moins un chiffre.",
            "password.symbols" => "Le mot de passe doit contenir au moins un caractère spécial.",
            "password.uncompromised" => "Ce mot de passe a été compromis dans une fuite de données. Veuillez en choisir un autre.",

            "password_confirmation.required" => "Veuillez confirmer votre mot de passe.",

            "new_password.required" => "Veuillez renseigner le nouveau mot de passe.",
            "new_password.confirmed" => "Les mots de passe ne correspondent pas.",
            "new_password.min" => "Le mot de passe doit contenir au moins 8 caractères.",
            
            "new_password_confirmation" => "Veuillez confirmer votre nouveau mot de passe.",

            "phone.required" => "Veuillez renseigner votre numéro de téléphone.",
            "phone.regex" => "Le numéro de téléphone doit commencer par 0 et doit contenir 10 chiffres.",

            "city.required" => "Veuillez renseigner le nom de votre ville.",
            "city.string" => "Votre ville renseigner doit être une chaîne de caractère.",
            "city.max" => "Votre nom de ville ne doit pas dépasser les 255 caractères.",

            "region.required" => "Veuillez sélectionner votre région.",
            "region.in" => "Veuillez sélectionner une région valide.",

            "zipcode.required" => "Veuillez renseigner votre code postal.",
            "zipcode.regex" => "Le code postal doit être composé de 5 chiffres.",

            // Login
            "email.exists" => "Oops ! Nous n'avons trouvé aucun compte associé à cette adresse email."
        ];
    }
}
