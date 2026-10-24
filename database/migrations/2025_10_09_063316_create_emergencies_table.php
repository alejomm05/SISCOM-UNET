<?php
// database/migrations/AAAA_BB_CC_create_emergencies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergencies', function (Blueprint $table) {
            $table->id();
            // Identifica al usuario que reporta
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            // Tipo de emergencia (ej. Médico, Incendio, Seguridad, etc.)
            $table->string('type'); 
            
            // Descripción breve o confirmación de la naturaleza del incidente
            $table->text('description')->nullable(); 
            
            // Ubicación reportada (puede ser una cadena de texto o coordenadas)
            $table->string('location')->nullable(); 
            
            // Estado de la emergencia: reportada, en curso, resuelta
            $table->enum('status', ['reported', 'in_progress', 'resolved'])->default('reported'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergencies');
    }
};