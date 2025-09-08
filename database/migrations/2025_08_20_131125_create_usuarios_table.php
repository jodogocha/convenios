<?php
// database/migrations/2025_08_20_131125_create_usuarios_table.php - VERSIÓN CORREGIDA

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            
            // Relación con roles - SIN constraint por ahora
            $table->unsignedBigInteger('rol_id');
            
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

        // Agregar la constraint de clave foránea en una operación separada
        // pero solo si la tabla roles existe
        if (Schema::hasTable('roles')) {
            try {
                Schema::table('usuarios', function (Blueprint $table) {
                    $table->foreign('rol_id', 'fk_usuarios_rol_id')
                          ->references('id')->on('roles')
                          ->onDelete('restrict');
                });
            } catch (Exception $e) {
                // Si falla, continuar sin la constraint
                // Se puede agregar manualmente después
            }
        }
    }

    public function down(): void
    {
        // Eliminar constraint primero si existe
        try {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropForeign('fk_usuarios_rol_id');
            });
        } catch (Exception $e) {
            // Si no existe, continuar
        }
        
        Schema::dropIfExists('usuarios');
    }
};