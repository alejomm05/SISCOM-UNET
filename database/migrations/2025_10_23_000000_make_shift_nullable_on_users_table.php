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
        // Para bases que ya corrieron add_shift_to_users_table cuando 'shift' era NOT NULL.
        // Sin esto, crear un guardia sin turno (o correr el seeder) falla en MySQL estricto.
        Schema::table('users', function (Blueprint $table) {
            $table->string('shift')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se revierte a NOT NULL: fallaría si ya hay guardias sin turno.
    }
};
