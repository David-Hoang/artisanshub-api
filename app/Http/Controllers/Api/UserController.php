<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
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


    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        try {
            $datasToStore = [
                "last_name" => $request->last_name,
                "first_name" => $request->first_name,
                "email" => $request->email,
                "username" => $request->username,
                "role" => $request->role,
                "password" => $request->password,
                "phone" => $request->phone,
                "city" => $request->city,
                "region" => $request->region,
                "zipcode" => $request->zipcode
            ];
            User::create($datasToStore);
            return response()->json(['message' => 'Un nouvel utilisateur a été ajouté'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Un problème est survenu sur le serveur'], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(int $userId)
    {
        try {
            $user = User::find($userId);
            if(!$user){
                return response()->json(['message' => 'Utilisateur introuvable'], 404);
            }else{
                return response()->json($user , 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Un problème est survenu sur le serveur'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $userId)
    {
        try {
            $user = User::find($userId);
            if(!$user){
                return response()->json(['message' => 'Utilisateur introuvable'], 404);
            }else{
                $datasToStore = [
                    "last_name" => $request->last_name,
                    "first_name" => $request->first_name,
                    "email" => $request->email,
                    "username" => $request->username,
                    "role" => $request->role,
                    "password" => $request->password,
                    "phone" => $request->phone,
                    "city" => $request->city,
                    "region" => $request->region,
                    "zipcode" => $request->zipcode
                ];
                $user->update($datasToStore);
                return response()->json(['message' => 'L\'utilisateur a été modifié'], 200);
            }

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Un problème est survenu sur le serveur'], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $userId)
    {
        try {
            $user = User::find($userId);
            if(!$user){
                return response()->json(['message' => 'Utilisateur introuvable'], 404);
            }else{
                $user->delete();
                return response()->json(['message' => 'Utilisateur supprimé avec succès'], 200);

            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Un problème est survenu sur le serveur'], 500);
        }
    }
}
