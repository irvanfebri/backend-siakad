<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required'
        ]);

        //ambil row paling atas
        $user = User::where('email', $request->email)->first();

        //jika user tidak ada 
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['email incorrect']
            ]);
        }

        //cocokin password,apakah sama dengan database
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['password incorrect']
            ]);
        }

        //kalo normal, generate token 
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(
            [
                'jwt-token' => $token,
                'user' => new UserResource($user),
            ]
        );
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'logout successfully',
        ]);
    }
}
