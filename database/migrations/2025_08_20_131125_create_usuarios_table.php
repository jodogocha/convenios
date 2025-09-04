<?php
// database/migrations/2024_01_01_000002_create_usuarios_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('telefono', 20)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            
            // Relación con roles
            $table->foreignId('rol_id')->constrained('roles')->onDelete('restrict');
            
            // Estados y seguridad
            $table->boolean('activo')->default(true);
            $table->boolean('email_verificado')->default(false);
            $table->timestamp('fecha_verificacion_email')->nullable();
            
            // Control de intentos de login
            $table->integer('intentos_fallidos')->default(0);
            $table->timestamp('bloqueado_hasta')->nullable();
            
            // Tokens para recuperación
            $table->string('token_recuperacion')->nullable();
            $table->timestamp('token_expiracion')->nullable();
            
            // Sesión
            $table->rememberToken();
            $table->timestamp('ultima_sesion')->nullable();
            $table->string('ip_ultima_sesion', 45)->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('username');
            $table->index('email');
            $table->index('activo');
            $table->index('rol_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};