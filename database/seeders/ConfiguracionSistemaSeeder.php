<?php
// database/seeders/ConfiguracionSistemaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracionSistema;

class ConfiguracionSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuraciones = [
            // Configuraciones de Seguridad
            [
                'clave' => 'max_intentos_login',
                'valor' => '5',
                'descripcion' => 'Máximo de intentos de login antes de bloquear cuenta',
                'tipo' => 'integer'
            ],
            [
                'clave' => 'tiempo_bloqueo_minutos',
                'valor' => '30',
                'descripcion' => 'Tiempo de bloqueo en minutos tras intentos fallidos',
                'tipo' => 'integer'
            ],
            [
                'clave' => 'duracion_sesion_horas',
                'valor' => '8',
                'descripcion' => 'Duración máxima de sesión en horas',
                'tipo' => 'integer'
            ],
            [
                'clave' => 'longitud_minima_password',
                'valor' => '8',
                'descripcion' => 'Longitud mínima de contraseña',
                'tipo' => 'integer'
            ],
            [
                'clave' => 'requerir_mayuscula_password',
                'valor' => 'true',
                'descripcion' => 'Requerir al menos una mayúscula en la contraseña',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'requerir_numero_password',
                'valor' => 'true',
                'descripcion' => 'Requerir al menos un número en la contraseña',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'requerir_simbolo_password',
                'valor' => 'true',
                'descripcion' => 'Requerir al menos un símbolo en la contraseña',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'requerir_cambio_password',
                'valor' => 'true',
                'descripcion' => 'Requerir cambio de contraseña en primer login',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'dias_expiracion_password',
                'valor' => '90',
                'descripcion' => 'Días antes de que expire una contraseña',
                'tipo' => 'integer'
            ],

            // Configuraciones del Sistema
            [
                'clave' => 'nombre_sistema',
                'valor' => 'Sistema de Convenios',
                'descripcion' => 'Nombre del sistema',
                'tipo' => 'string'
            ],
            [
                'clave' => 'version_sistema',
                'valor' => '1.0.0',
                'descripcion' => 'Versión actual del sistema',
                'tipo' => 'string'
            ],
            [
                'clave' => 'email_administrador',
                'valor' => 'admin@convenios.com',
                'descripcion' => 'Email del administrador del sistema',
                'tipo' => 'string'
            ],
            [
                'clave' => 'timezone',
                'valor' => 'America/Asuncion',
                'descripcion' => 'Zona horaria del sistema',
                'tipo' => 'string'
            ],
            [
                'clave' => 'idioma_sistema',
                'valor' => 'es',
                'descripcion' => 'Idioma por defecto del sistema',
                'tipo' => 'string'
            ],

            // Configuraciones de Auditoría
            [
                'clave' => 'auditoria_habilitada',
                'valor' => 'true',
                'descripcion' => 'Habilitar auditoría del sistema',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'dias_retencion_auditoria',
                'valor' => '365',
                'descripcion' => 'Días de retención de logs de auditoría',
                'tipo' => 'integer'
            ],
            [
                'clave' => 'dias_retencion_intentos_login',
                'valor' => '30',
                'descripcion' => 'Días de retención de intentos de login',
                'tipo' => 'integer'
            ],

            // Configuraciones de Notificaciones
            [
                'clave' => 'notificar_login_sospechoso',
                'valor' => 'true',
                'descripcion' => 'Notificar intentos de login sospechosos',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'notificar_cambios_usuario',
                'valor' => 'true',
                'descripcion' => 'Notificar cambios en usuarios',
                'tipo' => 'boolean'
            ],

            // Configuraciones específicas de Convenios
            [
                'clave' => 'convenios_requieren_aprobacion',
                'valor' => 'true',
                'descripcion' => 'Los convenios requieren aprobación antes de activarse',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'dias_vigencia_convenio_defecto',
                'valor' => '365',
                'descripcion' => 'Días de vigencia por defecto para convenios',
                'tipo' => 'integer'
            ],
            [
                'clave' => 'notificar_vencimiento_convenios',
                'valor' => 'true',
                'descripcion' => 'Notificar vencimiento próximo de convenios',
                'tipo' => 'boolean'
            ],
            [
                'clave' => 'dias_aviso_vencimiento_convenio',
                'valor' => '30',
                'descripcion' => 'Días de anticipación para avisar vencimiento',
                'tipo' => 'integer'
            ],
        ];

        foreach ($configuraciones as $config) {
            ConfiguracionSistema::updateOrCreate(
                ['clave' => $config['clave']],
                $config
            );
        }
    }
}