<?php
// database/seeders/RolesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'super_admin',
                'descripcion' => 'Administrador del sistema con acceso total',
                'activo' => true,
            ],
            [
                'nombre' => 'admin',
                'descripcion' => 'Administrador con acceso limitado',
                'activo' => true,
            ],
            [
                'nombre' => 'usuario',
                'descripcion' => 'Usuario estándar del sistema',
                'activo' => true,
            ],
            [
                'nombre' => 'invitado',
                'descripcion' => 'Usuario con acceso mínimo de solo lectura',
                'activo' => true,
            ],
        ];

        foreach ($roles as $rol) {
            Rol::updateOrCreate(
                ['nombre' => $rol['nombre']],
                $rol
            );
        }
    }
}