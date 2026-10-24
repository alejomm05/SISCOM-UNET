<?php
// database/migrations/AAAA_BB_CC_create_emergency_contacts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // Nombre del contacto (Ej: Dr. Pérez)
            $table->string('institution');      // Nombre de la institución
            $table->string('role');             // Rol (Ej: Médico, Policía, Bombero)
            $table->string('phone_number');     // Número de teléfono
            $table->string('whatsapp_link')->nullable(); // Link directo a WhatsApp
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
    }
};