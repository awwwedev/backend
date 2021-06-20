<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    use HasApiTokens;

    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $attr = $request->validate(
            [
                'email' => 'required|string|email',
                'password' => 'required|string|min:6'
            ]
        );

        if (!Auth::attempt($attr)) {
            return response(json_encode(['error' => 'Invalid credentials']), 401);
        }

        return [
            'token' => auth()->user()->createToken('API Token')->plainTextToken,
            'isAdmin' => Auth::user()->role->role === Role::ADMIN
        ];
    }

    /**
     * @return string[]
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Token revoked'
        ];
    }
}
