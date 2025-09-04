<?php
// database/seeders/UsuariosSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $superAdminRol = Rol::where('nombre', 'super_admin')->first();
        $adminRol = Rol::where('nombre', 'admin')->first();
        $usuarioRol = Rol::where('nombre', 'usuario')->first();

        $usuarios = [
            [
                'username' => 'superadmin',
                'email' => 'superadmin@convenios.com',
                'password' => Hash::make('SuperAdmin123!'),
                'nombre' => 'Super',
                'apellido' => 'Administrador',
                'telefono' => '+595981123456',
                'rol_id' => $superAdminRol->id,
                'activo' => true,
                'email_verificado' => true,
                'fecha_verificacion_email' => now(),
            ],
            [
                'username' => 'admin',
                'email' => 'admin@convenios.com',
                'password' => Hash::make('Admin123!'),
                'nombre' => 'Administrador',
                'apellido' => 'Sistema',
                'telefono' => '+595981234567',
                'rol_id' => $adminRol->id,
                'activo' => true,
                'email_verificado' => true,
                'fecha_verificacion_email' => now(),
            ],
            [
                'username' => 'usuario_demo',
                'email' => 'usuario@convenios.com',
                'password' => Hash::make('Usuario123!'),
                'nombre' => 'Usuario',
                'apellido' => 'Demostración',
                'telefono' => '+595981345678',
                'rol_id' => $usuarioRol->id,
                'activo' => true,
                'email_verificado' => true,
                'fecha_verificacion_email' => now(),
            ],
        ];

        foreach ($usuarios as $userData) {
            Usuario::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}