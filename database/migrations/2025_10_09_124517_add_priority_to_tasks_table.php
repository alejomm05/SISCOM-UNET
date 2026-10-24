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
        Schema::table('tasks', function (Blueprint $table) {
            // Valores: normal, high
            $table->string('priority')->default('normal')->after('due_date'); 
            // Opcional: para vincular la tarea a la emergencia que la originó
            $table->foreignId('emergency_id')->nullable()->constrained('emergencies'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['emergency_id']);
            $table->dropColumn('emergency_id');
            $table->dropColumn('priority');
        });
    }
};
