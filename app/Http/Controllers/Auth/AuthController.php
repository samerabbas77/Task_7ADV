<?php

namespace App\Http\Controllers\Auth;



use App\Services\Auth\AuthServices;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    protected $authService;
public function __construct(AuthServices $authService)
{
    $this->authService = $authService;
}

/**
* Get a JWT via given credentials.
*
* @param LoginRequest $request
* @return JsonResponse
*/
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->validated());
        if (!$token) {
        return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }
    /**
    * Get the authenticated User.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function info()
    {
        return response()->json(auth()->user());
    }
    /**
    * Log the user out (Invalidate the token).
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function logout()
    {
        $this->authService->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    /**
    * Refresh a token.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function refresh()
    {
       return $this->respondWithToken(Auth::refresh());
    }
    /**
    * Get the token array structure.
    *
    * @param string $token
    *
    * @return \Illuminate\Http\JsonResponse
    */
    protected function respondWithToken($token)
    {
        return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

}