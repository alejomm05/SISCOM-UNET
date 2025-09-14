<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\Controller as BaseController;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar login como admin
        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            return response()->json([
                'token' => $admin->createToken('auth-token')->plainTextToken,
                'user' => $admin,
                'role' => 'admin'
            ]);
        }

        // Intentar login como user (guardia)
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json([
                'token' => $user->createToken('auth-token')->plainTextToken,
                'user' => $user,
                'role' => 'guard'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Las credenciales son incorrectas'],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Cerraste sesión']);
    }
}