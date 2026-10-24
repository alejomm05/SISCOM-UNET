<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Agrega la columna 'shift' después de 'password'. 
            // Usamos string para el nombre del turno (ej: 'Mañana', 'Noche').
            // nullable: los guardias pueden no tener turno asignado (GuardiaController lo trata como opcional)
            $table->string('shift')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Elimina la columna 'shift' si se revierte la migración.
            $table->dropColumn('shift');
        });
    }
};