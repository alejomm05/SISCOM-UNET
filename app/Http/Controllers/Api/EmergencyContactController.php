<?php

namespace App\Http\Controllers\Api;

use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse; // Usar JsonResponse para respuestas controladas

class EmergencyContactController extends Controller
{
    /**
     * Obtiene todos los contactos de emergencia.
     * Esta ruta es accesible para cualquier usuario autenticado (Guardia y Supervisor).
     */
    public function index(): JsonResponse
    {
        return response()->json(EmergencyContact::all());
    }
    
    /**
     * Crea un nuevo contacto de emergencia.
     * Restringido solo para el Supervisor.
     */
    public function store(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'whatsapp_link' => 'nullable|url|max:255',
        ]);

        $contact = EmergencyContact::create($validated);
        return response()->json($contact, 201);
    }
    
    /**
     * Actualiza un contacto de emergencia existente.
     * Restringido solo para el Supervisor.
     */
    public function update(Request $request, $id): JsonResponse
    {

        
        $contact = EmergencyContact::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'whatsapp_link' => 'nullable|url|max:255',
        ]);
        
        $contact->update($validated);
        return response()->json($contact);
    }

    /**
     * Elimina un contacto de emergencia.
     * Restringido solo para el Supervisor.
     */
    public function destroy(Request $request, $id): JsonResponse
    {


        $contact = EmergencyContact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Contacto eliminado correctamente']);
    }
}