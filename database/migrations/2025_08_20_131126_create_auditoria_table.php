<?php
// database/migrations/2024_01_01_000005_create_auditoria_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->string('accion', 50);
            $table->string('tabla_afectada', 50)->nullable();
            $table->bigInteger('registro_id')->nullable();
            $table->json('valores_anteriores')->nullable();
            $table->json('valores_nuevos')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('fecha_hora')->useCurrent();
            
            $table->index('usuario_id');
            $table->index('accion');
            $table->index('tabla_afectada');
            $table->index('fecha_hora');
            $table->index('registro_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};