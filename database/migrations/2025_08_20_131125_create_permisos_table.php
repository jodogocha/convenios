<?php
// database/migrations/2024_01_01_000003_create_permisos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->text('descripcion')->nullable();
            $table->string('modulo', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index('nombre');
            $table->index('modulo');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};