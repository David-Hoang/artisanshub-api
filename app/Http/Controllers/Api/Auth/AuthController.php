<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\Role;
use App\Models\User;
use App\Enums\Region;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $req) {

        $inputsValidated = $req->validate([
            "first_name" => "required|string|max:255",
            "last_name" => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "username" => "nullable|string|max:255",
            "role" => ["required", Rule::in(Role::cases())],
            "password" => "required|string",
            "phone" => "required|string|max:15",
            "city" => "required|string|max:255",
            "region" => ["required", Rule::in(Region::cases())],
            "zipcode" => "required|string|max:5"
        ], $this->messages());

        try {
            $newUser = User::create($inputsValidated);
            if (!$newUser) {
                return response()->json([
                    "message" => "L'utilisateur n'a pas pu être créé."
                ], 500);
            }else{
                // Génération du token
                $userToken = $newUser->createToken($req->last_name);
                
                return response()->json([
                    "message" => "Utilisateur créé avec succès !",
                    "utilisateur" => $newUser,
                    "token" => $userToken->plainTextToken,
                ], 201);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de l'inscription."
            ], 500);
        }

    }

    public function login(Request $req) {
        
        $req->validate([
            "email" => "required|email|exists:users,email",
            "password" => "required"
        ], $this->messages());

        try {
            

            $user = User::where('email', $req->email)->first();

            if(!$user || !Hash::check($req->password, $user->password)){
                return response()->json([
                    "message" => "L'adresse email ou le mot n'est pas valide."
                ], 401);
            }else{
                $userToken = $user->createToken($user->last_name);
    
                return response()->json([
                    "message" => "Connexion réussie !",
                    "utilisateur" => $user,
                    "token" => $userToken->plainTextToken,
                ], 201);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "Une erreur s'est produite lors de la tentative de connexion."
            ], 500);
        }
    }
    
    public function logout(Request $req) {
        
        $req->user()->tokens()->delete();

        return response()->json(["message" => "Vous vous êtes déconnecté !"], 200);

    }

    public function allUsers () {
        try {
            $users = User::all();
            if(count($users) < 1){
                return response()->json(['message' => 'Aucun utilisateur existant'], 404);
            }else{
                return response()->json(User::all(), 200);
            }
        } 
        catch (\Throwable $th) {
            return response()->json(['message' => 'Un problème est survenu sur le serveur'], 500);
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
            "phone.max" => "Le numéro de téléphone ne dois pas dépasser 15 chiffres.",

            "city.required" => "Veuillez renseignere votre nom de ville.",
            "city.string" => "Votre ville renseigner doit être une chaîne de caractère.",
            "city.max" => "Votre nom de ville ne doit pas dépasser les 255 caractères.",

            "region.required" => "Veuillez sélectionner votre région.",
            "region.in" => "Veuillez sélectionner une région valide.",

            "zipcode.required" => "Veuillez renseigner votre code postal.",
            "zipcode.string" => "Le code postal doit être une chaîne de caractère.",
            "zipcode.max" => "Le code postal ne doit pas dépasser 5 caractères.",

            // Login
            "email.exists" => "Oops ! Nous n'avons trouvé aucun compte associé à cette adresse email."
        ];
    }
}
