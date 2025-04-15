<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\Role;
use App\Models\User;
use App\Enums\Region;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
                "password" => "required|string",
                "phone" => "required|regex:/^(0\d{9})$/",
                "city" => "required|string|max:255",
                "region" => ["required", Rule::in(Region::cases())],
                "zipcode" => "required|regex:/^\d{5}$/"
            ], $this->messages());
            
            $newUser = User::create($inputsValidated);

            if (!$newUser) 
                return response()->json([
                    "message" => "Une erreur est survenue lors de la création de l'utilisateur."
                ], 500);

            // Generate token
            $userToken = $newUser->createToken($newUser->id);
            
            return response()->json([
                "message" => "Utilisateur créé avec succès !",
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
            
            // Génération du token
            $userToken = $user->createToken($user->id);
    
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
            return response()->json(["message" => "Une erreur s'est produite lors de la tentiative de déconnexion."], 500);
        }
    }

    public function checkAuth (Request $req) {
        try {
            $user = $req->user();

            if($user) return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json(["message" => "Une erreur s'est produite lors de la tentiative de déconnexion."], 500);
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
            "password.string" => "Le mot de passe doit être une chaîne de caractère.",

            "phone.required" => "Veuillez renseigner votre numéro de téléphone.",
            "phone.regex" => "Le numéro de téléphone ne doit contenir 10 chiffres.",

            "city.required" => "Veuillez renseignere votre nom de ville.",
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
