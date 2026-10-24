<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Usaremos DB para TRUNCATE

class TaskAdminController extends Controller
{
    /**
     * Muestra el historial completo de todas las tareas (asignadas y completadas).
     */
    public function history()
    {
        // ... (lógica existente) ...
        $tasks = Task::with('user:id,name') 
                     ->orderByDesc('created_at')
                     ->paginate(20);
        
        $guardias = User::where('role', 'guardia')->get(['id', 'name']);

        return view('admin.tasks.history', compact('tasks', 'guardias'));
    }

    // ... (Método assign existente) ...
    public function assign(Request $request)
    {
        // ... (lógica de asignación existente) ...
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'priority' => 'required|in:normal,high,low',
        ]);
        
        $validated['status'] = 'pending';

        $dueDate = Carbon::parse($validated['due_date']);
        $validated['due_date'] = $dueDate->setTimezone('UTC');

        Task::create($validated);

        return redirect()->route('admin.tasks.history')->with('success', 'Tarea asignada exitosamente.');
    }

    /**
     * Elimina una tarea individual.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.tasks.history')->with('success', 'Tarea eliminada individualmente.');
    }

    /**
     * Elimina TODAS las tareas (VACIA la tabla).
     */
    public function clearAll()
    {
        // Esto es más rápido que DELETE y también resetea el AUTO_INCREMENT
        DB::table('tasks')->truncate(); 

        return redirect()->route('admin.tasks.history')->with('success', '¡Historial de tareas vaciado con éxito!');
    }
}