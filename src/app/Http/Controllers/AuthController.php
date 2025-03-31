<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
// use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Validation\ValidationException;




class AuthController extends Controller
{
    public $timestamps = false;

     /**
     * Registrar um novo usuário.
     */
    public function registrar(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'message' => 'Usuário registrado com sucesso!',
                'dados_usuario'=> $user
            ], 201);
        }catch (ValidationException $e) {
            return response()->json([
                'error' => 'Erro de validação',
                'messages' => $e->errors()
            ], 422);
        }
    }

    /**
     * Login e geração de token de acesso.
     */
    public function login(Request $request)
    {

            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            // Revoga todos os tokens do usuário anteriores
            $user->tokens->each(function ($token) {
                $token->delete();
            });


            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Usuário ou senha inválidos'], 401);
            }

            // Gera um token válido por 5 minutos
            $token = $user->createToken('auth_token', ['*'])->plainTextToken;

             // Define a expiração do token
            $user->update([
                'token_expires_at' => now()->addMinutes(5), // Expira em 5 minutos
                // 'token_gerador_user' => $user->id, // usuario do token
            ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 300, // 5 minutos
            ]);

    }


     /**
     * Logout do usuário - revoga todos os tokens gerados pelo usuario
     */
    public function logout(Request $request)
    {
            $user = $request->user(); // Obtém o usuário autenticado

            // if ($user) {
            //     $user->currentAccessToken()->delete(); // Revoga apenas o token atual
            // }

            // Revoga todos os tokens do usuário
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                'message' => 'Logout realizado com sucesso.'
            ], 200);

    }

    public function RenovarPeriodoToken(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado'], 401);
        }

        // Busca o token atual
        $token = $user->currentAccessToken();

        if (!$token) {
            return response()->json(['message' => 'Token inválido'], 401);
        }

        // Estende a expiração do token por mais 5 minutos
        $token->update(['expires_at' => now()->addMinutes(5)]);

        // Define a expiração do token
        $user->update([
            'token_expires_at' => now()->addMinutes(5) // Expira em 5 minutos
        ]);

        return response()->json([
            'message' => 'Token renovado com sucesso por mais 5 minutos!',
            'expires_in' => 300 // 5 minutos
        ]);
    }


}
