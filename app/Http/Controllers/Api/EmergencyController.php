<?php
// app/Http/Controllers/Api/EmergencyController.php

namespace App\Http\Controllers\Api;

use App\Models\Emergency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException; 

class EmergencyController extends Controller
{
     /**
     * Verifica si existe una emergencia activa y devuelve su información.
     */
    public function active(Request $request)
    {
        // Busca cualquier emergencia que NO esté resuelta
        $activeEmergency = Emergency::with('user:id,name,email')
            ->whereIn('status', ['reported', 'in_progress'])
            ->latest()
            ->first();
            
        // El frontend usará esta ruta para saber si debe mostrar el popup y bloquear el formulario.
        return response()->json($activeEmergency);
    }

    /**
     * Recibe y almacena un nuevo reporte de emergencia, con validación de "única activa".
     */
    public function store(Request $request)
    {
        // 🚨 VALIDACIÓN CRÍTICA: Bloquear si ya hay una emergencia activa
        $activeCount = Emergency::whereIn('status', ['reported', 'in_progress'])->count();

        if ($activeCount > 0) {
            throw ValidationException::withMessages([
                'emergency' => ['Ya existe una emergencia activa. Por favor, espere a que sea resuelta por el supervisor.'],
            ]);
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
        ]);
        
        $emergency = Emergency::create($validated);
        
        // 🚨 NO se crea actividad aquí. La actividad se crea cuando se MARCA COMO RESUELTA
        
        return response()->json([
            'message' => 'Reporte de emergencia enviado exitosamente. Supervisor notificado.',
            'emergency' => $emergency
        ], 201);
    }
    
    // Si necesitas ver la lista de emergencias
    public function index()
    {
        $emergencies = Emergency::with('user:id,name,email')
                                ->orderByDesc('created_at')
                                ->get();

        return response()->json($emergencies);
    }
}