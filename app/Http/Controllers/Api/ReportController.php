<?php

namespace App\Http\Controllers\Api;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller as BaseController;

class ReportController extends BaseController
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer', 
            'email' => 'required|email|exists:users,email', 
            'type' => 'required|in:sospechoso,daño,emergencia,otro',
            'description' => 'required|string',
            'location' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,mp3,mp4,mov|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('reports', 'public');
        }

        $locationJson = json_encode($request->location); 
        
        $report = Report::create([
            'user_id' => $request->user_id, 
            'type' => $request->type,
            'description' => $request->description,
            'location' => $locationJson,
            'image_path' => $filePath, // 🚨 CRÍTICO: Guarda la ruta de la imagen
            'status' => 'pending', 
        ]);

        return response()->json($report, 201);
    }

    // 🚨 MÉTODO SHOW: Muestra el detalle del reporte
    public function show($id)
    {
        // Cargar reporte y la relación 'user' (que contiene el email)
        $report = Report::with('user')->findOrFail($id);
        
        // Ocultar datos sensibles/innecesarios del usuario
        if ($report->user) {
            $report->user->makeHidden(['password', 'remember_token', 'api_token', 'code']);
        }

        return response()->json($report); // Esto incluye el campo image_path
    }
}