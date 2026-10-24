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
        Schema::table('emergencies', function (Blueprint $table) {
            // 1. Permitir Emergencias iniciadas por el Admin (user_id debe ser nullable)
            $table->foreignId('user_id')->nullable()->change(); 
            
            // 2. Agregar columna de seguimiento administrativo
            $table->timestamp('attended_at')->nullable()->after('status');
            
            // 3. Modificar enum de status si deseas más control, pero por ahora lo dejamos
            // $table->enum('status', ['reported', 'in_progress', 'resolved', 'cancelled'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergencies', function (Blueprint $table) {
            // Revertir a not nullable si es necesario. (Laravel no hace esto automáticamente)
            // Para simplificar, solo revertimos la columna administrativa
            $table->dropColumn('attended_at');
        });
    }
};