<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@energyno.ecosur.mx',
            'password' => Hash::make('derfg3wksbp6'),
            'role_id' => 1,
            'picture' => '../img/faces/face-0.jpg'
        ]);

        User::factory()->create([
            'id' => 2,
            'name' => 'Creator',
            'email' => 'creator@energyno.ecosur.mx',
            'password' => Hash::make('derfg3wksbp6'),
            'role_id' => 2,
            'picture' => '../img/faces/face-0.jpg'
        ]);

        User::factory()->create([
            'id' => 3,
            'name' => 'Member',
            'email' => 'member@energyno.ecosur.mx',
            'password' => Hash::make('derfg3wksbp6'),
            'role_id' => 3,
            'picture' => '../img/faces/face-0.jpg'
        ]);

       
    }
}
