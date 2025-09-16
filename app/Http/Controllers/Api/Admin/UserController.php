<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


use App\Http\Controllers\Controller as BaseController;

class UserController extends BaseController
{
    use AuthorizesRequests;
    public function index()
    {
        
        // Solo admins pueden ver esto
        $this->authorize('viewAny', User::class);

        return User::select('id', 'name', 'email', 'created_at')->get();
    }

    public function store(Request $request)
    {
        // Solo admins pueden crear guardias
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'code' => 'required|string', // Código de invitación
            'password' => 'required|min:8',
        ]);

        // Verificar código secreto
        if ($request->code !== 'INVITE-GUARD-2025') {
            return response()->json(['error' => 'Código inválido'], 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'code' => $request->code,
            'password' => Hash::make($request->password),
        ]);

        return response()->json($user, 201);
    }

    public function destroy($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'Usuario no encontrado'], 404);
    }

    $user->delete();

    return response()->json(['message' => 'Usuario eliminado correctamente']);
}

}