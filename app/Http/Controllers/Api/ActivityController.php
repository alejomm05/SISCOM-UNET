<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    public function index()
    {
        // 1. Consultar Mensajes
        $messages = Message::select('id', 'content', 'created_at', DB::raw("'message' as type"), 'user_id')
            ->whereNotNull('content') // Excluir mensajes solo con archivos si no quieres mostrarlos
            ->limit(50); // Limite inicial

        // 2. Consultar Reportes
        $reports = Report::select('id', 'description AS content', 'created_at', DB::raw("'report' as type"), 'user_id')
            ->limit(50); // Limite inicial

        // 3. Consultar Creación de Usuarios (Avisos)
        // Usamos el campo 'name' como el contenido del aviso
        $userCreations = User::select('id', DB::raw("CONCAT('Nuevo guardia: ', name) AS content"), 'created_at', DB::raw("'new_user' as type"), 'id as user_id')
            ->limit(50); // Limite inicial

        // 4. Unir las consultas y ordenar
        $activities = $reports
            ->unionAll($messages)
            ->unionAll($userCreations)
            ->orderBy('created_at', 'desc') // Los más recientes primero
            ->limit(100) // Límite total de actividades a mostrar
            ->get();

        return response()->json($activities);
    }
}