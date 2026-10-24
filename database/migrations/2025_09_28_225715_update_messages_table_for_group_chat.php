<?php
// database/migrations/XXXXXXXX_XX_XX_update_messages_table_for_group_chat.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aplica los cambios a la tabla.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // 1. Eliminar claves foráneas y columnas viejas
            $table->dropForeign(['sender_id']);
            $table->dropColumn('sender_id');
            
            // La columna receiver_id puede ser nula, por lo que dropForeign necesita un array
            $table->dropForeign(['receiver_id']); 
            $table->dropColumn('receiver_id');
            
            // 2. Renombrar columnas existentes
            // Renombrar 'body' a 'content' y hacerlo nullable
            $table->renameColumn('body', 'content');
            
            // Renombrar 'media_url' a 'file_path'
            $table->renameColumn('media_url', 'file_path');
            
            // Cambiar la definición de las columnas renombradas para que coincidan con el objetivo
            $table->text('content')->nullable()->change();
            $table->string('file_path')->nullable()->change();

            // 3. Añadir nuevas columnas
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('file_type')->nullable()->after('file_path'); // 'image', 'audio', 'video'
        });
    }

    /**
     * Revierte los cambios de la tabla.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // 1. Revertir las columnas añadidas
            $table->dropColumn('user_id');
            $table->dropColumn('file_type');
            
            // 2. Revertir el renombrado
            // Revertir el tipo de dato y el nombre para que body no sea nullable
            $table->text('content')->nullable(false)->change(); 
            $table->renameColumn('content', 'body');
            $table->renameColumn('file_path', 'media_url');

            // 3. Recrear las columnas viejas
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }
};