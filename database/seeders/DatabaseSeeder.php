<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            PermisosSeeder::class,
            UsuariosSeeder::class,
            ConfiguracionSistemaSeeder::class,
            ConveniosSeeder::class, // Nuevo seeder agregado
            InformesSeeder::class, // Nuevo seeder agregado
        ]);
    }
}