<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Emergency;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Events\NewEmergencyTaskAssigned;    

class EmergencyAdminController extends Controller
{
    /**
     * Muestra el panel de gestión de emergencias.
     */
    public function index()
    {
        // Trae todas las emergencias y el guardia que la reportó
        $emergencies = Emergency::with('user')
                                ->orderBy('status')
                                ->orderByDesc('created_at')
                                ->get();
        
        // Necesitamos la lista de guardias para el formulario de asignación/respuesta rápida
        $guardias = User::where('role', 'guardia')->get(['id', 'name']);

        return view('admin.emergencies.index', compact('emergencies', 'guardias'));
    }

    /**
     * Envía una emergencia desde el panel del administrador.
     */
    public function store(Request $request)
    {
        // El admin no necesita validación de "única activa"
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            // Opcional: El admin puede especificar una ubicación si lo desea
            'location' => 'nullable|string|max:255', 
        ]);
        
        // El user_id es NULL porque fue iniciado por el admin
        $validated['user_id'] = null; 
        $validated['status'] = 'reported'; 
        
        $emergency = Emergency::create($validated);
        
        // 🚨 AQUÍ DEBE IR LA LÓGICA DE BROADCASTING
        // event(new \App\Events\NewEmergencyReported($emergency)); 

        return redirect()->route('admin.emergencies.index')->with('success', 'Emergencia administrativa enviada y notificada a los guardias.');
    }

    /**
     * Marca una emergencia como atendida y completada (resuelve).
     */
    public function resolve(Emergency $emergency)
    {
        $emergency->update([
            'status' => 'resolved',
            'attended_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.emergencies.index')->with('success', 'Emergencia marcada como resuelta.');
    }
    
    /**
     * Asigna la emergencia a un guardia como tarea de alta prioridad.
     */
       public function assignTask(Request $request, Emergency $emergency)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);
        
        // Crear la tarea de alta prioridad
        $task = Task::create([ // 💡 Capturamos el objeto $task
            'assigned_to' => $validated['assigned_to'],
            'title' => "ATENDER EMERGENCIA: {$emergency->type}",
            'description' => "Origen: Emergencia #{$emergency->id}. Descripción: {$emergency->description}. Ubicación: {$emergency->location}",
            'due_date' => Carbon::now()->addHours(2), 
            'priority' => 'high', // ESTO LO VERÁ EL GUARIDA EN ROJO EN FLUTTER
            'emergency_id' => $emergency->id,
            'status' => 'pending',
        ]);
        
        // Opcional: Cambiar el estado de la emergencia a "in_progress"
        $emergency->update(['status' => 'in_progress']);

        // 🚨 PASO CRÍTICO: Disparar el evento de Broadcasting
        event(new NewEmergencyTaskAssigned($task));

        return redirect()->route('admin.emergencies.index')->with('success', 'Emergencia asignada como tarea de alta prioridad y notificada al guardia en tiempo real.');
    }


}