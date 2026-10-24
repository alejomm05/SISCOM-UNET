<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar entrada
        $request->validate([
            'user_id' => 'required|integer|exists:users,id', 
            'content' => 'required_without:file|nullable|string|max:1000',
            'file' => 'required_without:content|nullable|file|mimes:jpg,jpeg,png,mp3,mp4,mov,webm|max:20480', // 20MB
            'file_type' => 'required_with:file|in:image,audio,video',
        ]);

        $filePath = null;
        
        // 2. Manejar la subida del archivo (si existe)
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('messages', 'public');
        }

        // 3. Crear el mensaje
        $message = Message::create([
            'user_id' => $request->user_id, 
            'content' => $request->content, 
            'file_path' => $filePath, // 🚨 CRÍTICO: Guarda la ruta del archivo
            'file_type' => $request->file_type,
        ]);

        // 4. Broadcast
        $message->load('user');

        broadcast(new MessageSent($message));

        return response()->json($message, 201);
    }

    // Cargar historial de mensajes
    public function index()
    {
        $messages = Message::with('user')
            ->orderBy('created_at', 'asc')
            ->limit(50) 
            ->get();

        return response()->json($messages);
    }

    // 🚨 MÉTODO SHOW: Muestra el detalle del mensaje
    public function show($id)
    {
        // Cargar mensaje y la relación 'user' (que contiene el email)
        $message = Message::with('user')->findOrFail($id);
        
        // Ocultar datos sensibles/innecesarios del usuario
        if ($message->user) {
            $message->user->makeHidden(['password', 'remember_token', 'api_token', 'code']);
        }

        return response()->json($message);
    }
}