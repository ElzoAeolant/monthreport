<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        DB::table('tags')->truncate();
        //DB::table('item_tag')->truncate();
        //DB::table('categories')->truncate();
        //DB::table('items')->truncate();

        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            DevicesSeeder::class
        ]);

        //$this->call([RolesTableSeeder::class, UsersTableSeeder::class, DevicesSeeder::class]);
        //ini_set('memory_limit', '-1');

        //DB::unprepared(file_get_contents(__dir__ . '/cat_ageeml.sql'));

        // DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
