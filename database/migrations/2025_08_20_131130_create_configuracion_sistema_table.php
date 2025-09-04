<?php
// database/migrations/2024_01_01_000007_create_configuracion_sistema_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->text('valor')->nullable();
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['string', 'integer', 'boolean', 'json'])->default('string');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            $table->index('clave');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_sistema');
    }
};