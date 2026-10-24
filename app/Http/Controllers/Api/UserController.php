<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse; // Usar JsonResponse para respuestas consistentes

class UserController extends BaseController
{
    // index, store y destroy solo son accesibles con un token de supervisor
    // (middleware 'abilities:supervisor' en routes/api.php).

    /**
     * Muestra una lista de usuarios (guardias).
     */
    public function index(Request $request): JsonResponse
    {
        // Obtener solo los usuarios con rol 'guardia' para esta pantalla de gestión.
        $users = User::where('role', 'guardia')
                     ->select('id', 'name', 'email', 'created_at')
                     ->get();

        return response()->json($users);
    }

    /**
     * Almacena un nuevo usuario (guardia).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'code' => 'required|string', 
            'password' => 'required|min:8',
        ]);

        if ($validated['code'] !== 'INVITE-GUARD-2025') {
            return response()->json(['message' => 'Código de invitación inválido'], 403);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'code' => $validated['code'],
            'password' => Hash::make($validated['password']),
            'role' => 'guardia', // Asignación de rol
        ]);

        return response()->json($user, 201);
    }
    
    /**
     * Muestra el detalle de un usuario. (Usado para actividades/reportes).
     */
    public function show($id): JsonResponse
    {
        // Este método se deja sin restricción de rol ya que se usa para mostrar el detalle de actividades.
        $user = User::select('id', 'name', 'email', 'created_at', 'role')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Elimina un usuario.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        
        // Bloquear eliminación si intenta eliminar a otro supervisor.
        if ($user->role === 'supervisor') {
             return response()->json(['message' => 'No puedes eliminar a otro supervisor.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
