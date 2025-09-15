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
        Schema::create('reports', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('type'); // "incendio", "robo", etc.
    $table->text('description');
    $table->json('location'); // { "lat": -12.34, "lng": -77.56 }
    $table->string('media_url')->nullable();
    $table->enum('status', ['pending', 'resolved'])->default('pending');
    $table->string('image_path')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
