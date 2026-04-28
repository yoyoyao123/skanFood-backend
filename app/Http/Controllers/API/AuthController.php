<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'restaurant_name' => 'required|string',
        ]);

        return DB::transaction(function () use ($fields) {
            // 1. Création du Restaurateur
            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
                'role' => 'owner'
            ]);

        
            $user->restaurant()->create([
                'name' => $fields['restaurant_name'],
                'slug' => Str::slug($fields['restaurant_name']) . '-' . rand(1000, 9999),
                'is_active' => true,
            ]);

            $token = $user->createToken('skantoken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Compte et restaurant créés !',
                'data' => ['user' => $user, 'token' => $token]
            ], 201);
        });
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();

        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        $token = $user->createToken('skantoken')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->load('restaurant'), 
                'token' => $token
            ]
        ]);
    }
}