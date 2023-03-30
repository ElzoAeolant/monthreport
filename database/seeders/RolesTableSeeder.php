<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'id' => 1,
            'name' => 'Administrador',
            'description' => 'Este rol tiene acceso a todas las configuraciones.',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('roles')->insert([
            'id' => 2,
            'name' => 'Gestión',
            'description' => 'Este rol tiene acceso limitado a la creación y configuración de recursos.',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('roles')->insert([
            'id' => 3,
            'name' => 'Miembro',
            'description' => 'Este rol tiene acceso de solo vista.',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
