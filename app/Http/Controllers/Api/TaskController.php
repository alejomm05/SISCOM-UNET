<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TaskController extends Controller
{
    // Listar tareas del usuario (Hoy y Futuras)
    public function index(Request $request)
    {
        $tasks = Task::with('user:id,name') // Incluimos la relación del usuario asignado (assigned_to)
                     ->where('assigned_to', $request->user()->id)
                     ->where('status', 'pending') 
                     ->where('due_date', '>=', now()->startOfDay())
                     ->orderBy('due_date', 'asc')
                     ->get();

        return response()->json($tasks);
    }

    // Crear nueva tarea
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        try {
            $dueDate = Carbon::parse($request->due_date); 
            
            if (!$dueDate->isUtc()) {
                // Guardamos en UTC para compatibilidad
                $dueDate = $dueDate->setTimezone('UTC'); 
            }
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Formato de fecha de vencimiento inválido.'], 400);
        }

        $task = Task::create([
            'assigned_to' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description ?? '', // la columna es NOT NULL
            'due_date' => $dueDate, 
            'status' => 'pending',
            'priority' => 'normal', // Las tareas creadas por el guardia son normales
        ]);
        
        // Recargar con la relación para el JSON de respuesta
        $task->load('user:id,name');

        return response()->json(['task' => $task->toArray()], 201); // Devolvemos el objeto en la clave 'task'
    }

    // Marcar como completada
    public function update(Request $request, $id)
    {
        // Solo puede completar sus propias tareas
        $task = Task::where('assigned_to', $request->user()->id)
                    ->where('id', $id)
                    ->firstOrFail();

        $task->update(['status' => 'completed']);

        return response()->json($task);
    }
}