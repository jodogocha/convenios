<?php
// database/seeders/PermisosSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permiso;
use App\Models\Rol;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            // Módulo Usuarios
            ['nombre' => 'usuarios.crear', 'descripcion' => 'Crear usuarios', 'modulo' => 'usuarios'],
            ['nombre' => 'usuarios.leer', 'descripcion' => 'Ver usuarios', 'modulo' => 'usuarios'],
            ['nombre' => 'usuarios.actualizar', 'descripcion' => 'Actualizar usuarios', 'modulo' => 'usuarios'],
            ['nombre' => 'usuarios.eliminar', 'descripcion' => 'Eliminar usuarios', 'modulo' => 'usuarios'],
            
            // Módulo Roles
            ['nombre' => 'roles.crear', 'descripcion' => 'Crear roles', 'modulo' => 'roles'],
            ['nombre' => 'roles.leer', 'descripcion' => 'Ver roles', 'modulo' => 'roles'],
            ['nombre' => 'roles.actualizar', 'descripcion' => 'Actualizar roles', 'modulo' => 'roles'],
            ['nombre' => 'roles.eliminar', 'descripcion' => 'Eliminar roles', 'modulo' => 'roles'],
            
            // Módulo Permisos
            ['nombre' => 'permisos.gestionar', 'descripcion' => 'Gestionar permisos', 'modulo' => 'permisos'],
            
            // Módulo Seguridad
            ['nombre' => 'seguridad.configurar', 'descripcion' => 'Configurar seguridad', 'modulo' => 'seguridad'],
            ['nombre' => 'auditoria.ver', 'descripcion' => 'Ver logs de auditoría', 'modulo' => 'seguridad'],
            
            // Módulo Reportes
            ['nombre' => 'reportes.ver', 'descripcion' => 'Ver reportes del sistema', 'modulo' => 'reportes'],
            ['nombre' => 'reportes.exportar', 'descripcion' => 'Exportar reportes', 'modulo' => 'reportes'],
            
            // Módulo Convenios (específico para tu sistema)
            ['nombre' => 'convenios.crear', 'descripcion' => 'Crear convenios', 'modulo' => 'convenios'],
            ['nombre' => 'convenios.leer', 'descripcion' => 'Ver convenios', 'modulo' => 'convenios'],
            ['nombre' => 'convenios.actualizar', 'descripcion' => 'Actualizar convenios', 'modulo' => 'convenios'],
            ['nombre' => 'convenios.eliminar', 'descripcion' => 'Eliminar convenios', 'modulo' => 'convenios'],
            ['nombre' => 'convenios.aprobar', 'descripcion' => 'Aprobar convenios', 'modulo' => 'convenios'],
            
            // Módulo Configuración
            ['nombre' => 'configuracion.ver', 'descripcion' => 'Ver configuración del sistema', 'modulo' => 'configuracion'],
            ['nombre' => 'configuracion.actualizar', 'descripcion' => 'Actualizar configuración', 'modulo' => 'configuracion'],
        ];

        // Crear permisos
        foreach ($permisos as $permiso) {
            Permiso::updateOrCreate(
                ['nombre' => $permiso['nombre']],
                $permiso
            );
        }

        // Asignar permisos a roles
        $this->asignarPermisosARoles();
    }

    private function asignarPermisosARoles(): void
    {
        // Super Admin: todos los permisos
        $superAdmin = Rol::where('nombre', 'super_admin')->first();
        if ($superAdmin) {
            $todosLosPermisos = Permiso::all()->pluck('id');
            $superAdmin->permisos()->sync($todosLosPermisos);
        }

        // Admin: permisos limitados
        $admin = Rol::where('nombre', 'admin')->first();
        if ($admin) {
            $permisosAdmin = Permiso::whereIn('nombre', [
                'usuarios.crear', 'usuarios.leer', 'usuarios.actualizar',
                'roles.leer', 
                'convenios.crear', 'convenios.leer', 'convenios.actualizar', 'convenios.aprobar',
                'reportes.ver', 'reportes.exportar',
                'auditoria.ver',
                'configuracion.ver',
            ])->pluck('id');
            $admin->permisos()->sync($permisosAdmin);
        }

        // Usuario: permisos básicos
        $usuario = Rol::where('nombre', 'usuario')->first();
        if ($usuario) {
            $permisosUsuario = Permiso::whereIn('nombre', [
                'usuarios.leer',
                'convenios.crear', 'convenios.leer', 'convenios.actualizar',
                'reportes.ver',
            ])->pluck('id');
            $usuario->permisos()->sync($permisosUsuario);
        }

        // Invitado: solo lectura
        $invitado = Rol::where('nombre', 'invitado')->first();
        if ($invitado) {
            $permisosInvitado = Permiso::whereIn('nombre', [
                'convenios.leer',
                'reportes.ver',
            ])->pluck('id');
            $invitado->permisos()->sync($permisosInvitado);
        }
    }
}