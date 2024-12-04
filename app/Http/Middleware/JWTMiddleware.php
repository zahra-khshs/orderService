<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class JWTMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            if (!$user = FacadesJWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (Exception $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        }

        $request->user = $user;
        return $next($request);
    }
}
