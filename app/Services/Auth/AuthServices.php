<?php
namespace App\Services\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Api\ProjectService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthServices
{
        /*
        @param array $credentials
        @return $token
        */
        public function login(array $credentials): ?string
        {
            if ($token = Auth::attempt($credentials)) {
            return $token;
            }
            return null;
        }


        public function logout()
        {
            $user = Auth::user();

            auth()->logout();

        }

}