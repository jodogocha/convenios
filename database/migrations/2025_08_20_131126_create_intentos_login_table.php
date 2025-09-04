<?php
// database/migrations/2024_01_01_000006_create_intentos_login_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intentos_login', function (Blueprint $table) {
            $table->id();
            $table->string('email_o_username', 150);
            $table->string('ip_address', 45)->nullable();
            $table->boolean('exitoso')->default(false);
            $table->string('mensaje')->nullable();
            $table->timestamp('fecha_intento')->useCurrent();
            
            $table->index('email_o_username');
            $table->index('ip_address');
            $table->index('fecha_intento');
            $table->index('exitoso');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intentos_login');
    }
};