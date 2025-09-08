<?php
// database/migrations/2025_09_08_create_informes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informes', function (Blueprint $table) {
            $table->id();
            
            // Información básica del informe
            $table->foreignId('convenio_id')->constrained('convenios')->onDelete('restrict');
            $table->string('institucion_co_celebrante');
            $table->string('unidad_academica');
            $table->string('carrera');
            $table->date('fecha_celebracion');
            $table->string('vigencia')->nullable();
            
            // Periodo evaluado
            $table->string('periodo_evaluado');
            $table->date('periodo_desde')->nullable();
            $table->date('periodo_hasta')->nullable();
            
            // Responsables
            $table->string('dependencia_responsable');
            $table->json('coordinadores_designados'); // Array de coordinadores
            $table->string('convenio_celebrado_propuesta');
            
            // Tipo de convenio
            $table->enum('tipo_convenio', ['Marco', 'Específico']);
            
            // Ejecución del convenio
            $table->boolean('convenio_ejecutado')->default(true);
            
            // Si el convenio se ejecutó
            $table->integer('numero_actividades_realizadas')->nullable();
            $table->text('logros_obtenidos')->nullable();
            $table->text('beneficios_alcanzados')->nullable();
            $table->text('dificultades_incidentes')->nullable();
            $table->text('responsabilidad_instalaciones')->nullable();
            $table->text('sugerencias_mejoras')->nullable();
            $table->string('anexo_evidencias')->nullable(); // URL de Google Drive
            $table->text('informacion_complementaria')->nullable();
            
            // Si el convenio NO se ejecutó
            $table->text('motivos_no_ejecucion')->nullable();
            $table->text('propuestas_mejoras')->nullable();
            $table->text('informacion_complementaria_no_ejecutado')->nullable();
            
            // Anexos y evidencias
            $table->json('anexos')->nullable(); // URLs de archivos, fotos, videos, etc.
            $table->string('enlace_google_drive')->nullable();
            
            // Firmas y fechas
            $table->json('firmas')->nullable(); // Array de firmas
            $table->date('fecha_presentacion');
            $table->text('observaciones')->nullable();
            
            // Estado del informe
            $table->enum('estado', ['borrador', 'enviado', 'aprobado', 'rechazado'])
                  ->default('borrador');
            
            // Usuario que crea y revisa
            $table->foreignId('usuario_creador_id')->constrained('usuarios')->onDelete('restrict');
            $table->foreignId('usuario_revisor_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamp('fecha_revision')->nullable();
            
            // Metadatos
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('convenio_id');
            $table->index('estado');
            $table->index('fecha_presentacion');
            $table->index('usuario_creador_id');
            $table->index(['periodo_desde', 'periodo_hasta']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informes');
    }
};