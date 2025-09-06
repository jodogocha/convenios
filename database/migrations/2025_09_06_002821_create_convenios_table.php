<?php
// database/migrations/2025_09_06_create_convenios_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('convenios', function (Blueprint $table) {
            $table->id();
            
            // Información básica del convenio
            $table->string('institucion_contraparte');
            $table->string('tipo_convenio', 100);
            $table->text('objeto');
            $table->date('fecha_firma');
            $table->date('fecha_vencimiento')->nullable();
            $table->boolean('vigencia_indefinida')->default(false);
            
            // Coordinación y responsables
            $table->string('coordinador_convenio', 100);
            $table->string('pais_region', 100);
            
            // Signatarios (JSON para múltiples signatarios)
            $table->json('signatarios');
            
            // Archivo del convenio firmado
            $table->string('archivo_convenio_path')->nullable();
            $table->string('archivo_convenio_nombre')->nullable();
            $table->bigInteger('archivo_convenio_size')->nullable();
            
            // Dictamen y versión final
            $table->string('dictamen_numero')->nullable();
            $table->boolean('version_final_firmada')->default(false);
            
            // Estado y numeración automática
            $table->string('numero_convenio')->unique()->nullable(); // Se genera automáticamente
            $table->enum('estado', ['borrador', 'pendiente_aprobacion', 'aprobado', 'activo', 'vencido', 'cancelado'])
                  ->default('borrador');
            
            // Notificaciones y seguimiento
            $table->boolean('notificaciones_enviadas')->default(false);
            $table->timestamp('fecha_notificacion_ori')->nullable();
            $table->timestamp('fecha_notificacion_ua')->nullable();
            
            // Usuarios responsables
            $table->foreignId('usuario_creador_id')->constrained('usuarios')->onDelete('restrict');
            $table->foreignId('usuario_coordinador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('usuario_aprobador_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamp('fecha_aprobacion')->nullable();
            
            // Metadatos adicionales
            $table->text('observaciones')->nullable();
            $table->json('metadata')->nullable(); // Para datos adicionales flexibles
            
            $table->timestamps();
            
            // Índices
            $table->index('institucion_contraparte');
            $table->index('tipo_convenio');
            $table->index('estado');
            $table->index('fecha_firma');
            $table->index('fecha_vencimiento');
            $table->index('usuario_creador_id');
            $table->index('numero_convenio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('convenios');
    }
};