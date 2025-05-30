<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = $request->user();
            if(!$user || $user->role->value !== 'client'){
                return response()->json([
                    'message' => "Accès refusé, vous n'avez pas le rôle nécessaire.",
                ], 403);
            }
            return $next($request);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => "Une erreur est survenue lors de la vérification du rôle.",
            ], 500);
        }
    }
}
