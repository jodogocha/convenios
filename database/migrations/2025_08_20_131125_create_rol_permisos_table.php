<?php
// database/migrations/2024_01_01_000004_create_rol_permisos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['rol_id', 'permiso_id']);
            $table->index('rol_id');
            $table->index('permiso_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_permisos');
    }
};