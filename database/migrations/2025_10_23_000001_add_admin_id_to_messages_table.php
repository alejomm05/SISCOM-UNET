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
        // Permite que los supervisores (tabla admins) escriban en el chat.
        // Cada mensaje tiene exactamente un autor: user_id (guardia) o admin_id (supervisor).
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('admin_id')->nullable()->after('user_id')->constrained('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
            // user_id se deja nullable: volver a NOT NULL fallaría si hay mensajes de supervisores
        });
    }
};
