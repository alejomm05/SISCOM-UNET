<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller as BaseController;

class LoginController extends BaseController
{
    /**
     * Maneja la autenticación para Guardias (Users) y Supervisores (Admins).
     * Inyecta el campo 'role' en el objeto del usuario antes de devolver la respuesta.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // --- 1. INTENTO DE LOGIN COMO ADMIN (SUPERVISOR) ---
            $admin = Admin::where('email', $request->email)->first();

            if ($admin && Hash::check($request->password, $admin->password)) {
                
                // 🚨 CORRECCIÓN CLAVE: Inyectar dinámicamente la propiedad 'role'
                $admin->role = 'supervisor'; 
                
                return response()->json([
                    'token' => $admin->createToken('auth-token', ['supervisor'])->plainTextToken,
                    'user' => $admin, // El modelo 'Admin' serializado ahora incluye 'role: supervisor'
                ], 200);
            }

            // --- 2. INTENTO DE LOGIN COMO USER (GUARDIA) ---
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                
                // 🚨 CORRECCIÓN CLAVE: Inyectar dinámicamente la propiedad 'role'
                $user->role = 'guard'; 
                
                return response()->json([
                    'token' => $user->createToken('auth-token', ['guard'])->plainTextToken,
                    'user' => $user, // El modelo 'User' serializado ahora incluye 'role: guard'
                ], 200);
            }

            // 3. FALLO EN AMBOS INTENTOS
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas'],
            ]);

        } catch (ValidationException $e) {
            // Manejar la excepción de validación (422)
            return response()->json([
                'message' => 'Fallo en la autenticación.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            // Capturar errores fatales del servidor (500)
            Log::error('FATAL LOGIN ERROR: ' . $e->getMessage() . ' on file ' . $e->getFile() . ' at line ' . $e->getLine());
            
            return response()->json([
                'message' => 'Error interno del servidor.',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        // ... (Tu lógica de logout es correcta)
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Cerraste sesión']);
    }
}
