<?php

namespace Database\Seeders;

use App\Models\Device;
use Illuminate\Database\Seeder;

class DevicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Device::factory()
        ->count(150)
        ->sequence(
            fn ($sequence) => ["label" => "Energyno ".str_pad(($sequence->index+1),3,'0',STR_PAD_LEFT)],
        )
        ->create();

        /*DB::table('devices')->insert([
            'id' => "20",
            'token' => "4d1c09446c0214eff56bac5ed4e1917f",
            'label' => "Energyno 020",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "21",
            'token' => "5ab442589d2d2446af98f34d67f1d9ec",
            'label' => "Energyno 021",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "22",
            'token' => "72db9998aa49df983175fe9e0644a412",
            'label' => "Energyno 022",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "23",
            'token' => "636f1f499c2fb485fec4cc569582a1a8",
            'label' => "Energyno 023",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "24",
            'token' => "9699263bd3284fa38571e032e02f8167",
            'label' => "Energyno 024",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "25",
            'token' => "21be2595dfc0157d3f5289fa5302cf7c",
            'label' => "Energyno 025",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "26",
            'token' => "2b9eb1cd7b49273ac5c1fc6e4316baec",
            'label' => "Energyno 026",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "27",
            'token' => "d4402ab41ec1084d966ea675600f5aec",
            'label' => "Energyno 027",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "28",
            'token' => "1d41ceef09655ce2bbc9a518afc3c285",
            'label' => "Energyno 028",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "29",
            'token' => "dbd647e0f4f650d30a6ed026b58c0ec0",
            'label' => "Energyno 029",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "30",
            'token' => "ddd419e9d134e28cf0d5a2194ea7a720",
            'label' => "Energyno 030",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "31",
            'token' => "bbfe47578a3f63a6d39589fdd4bb2aa9",
            'label' => "Energyno 031",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "32",
            'token' => "3d600c83b5d19add5d63602bda4c2316",
            'label' => "Energyno 032",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "33",
            'token' => "01d6dcd710921f25c25940d3988ccfe4",
            'label' => "Energyno 033",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "34",
            'token' => "aa9cf073582fad8fd16f2b0c55c13249",
            'label' => "Energyno 034",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "35",
            'token' => "58f1064f168b077801c79ddb2a7c3206",
            'label' => "Energyno 035",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "36",
            'token' => "36364481c809d002e76b7176822d49b1",
            'label' => "Energyno 036",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "37",
            'token' => "e81529da09cb5e6f7abf340639ae8761",
            'label' => "Energyno 037",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "38",
            'token' => "1f7a6b5dff52844d967231fc381c4661",
            'label' => "Energyno 038",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "39",
            'token' => "bdb413382b72ae2ecca015a9a0cf1f4f",
            'label' => "Energyno 039",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "40",
            'token' => "c167bf11bc5658b06ab3cfefde5a1a12",
            'label' => "Energyno 040",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "41",
            'token' => "b43442e52f427d27ea1510770cad5b44",
            'label' => "Energyno 041",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "42",
            'token' => "67e8b0ad4efe7499946e880709009ce0",
            'label' => "Energyno 042",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "43",
            'token' => "41d70fc6e610876c88a00e75aef85d48",
            'label' => "Energyno 043",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "44",
            'token' => "ff59f986a4e19df13a034fdfff3ec9f6",
            'label' => "Energyno 044",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "45",
            'token' => "c0b9aefd74bf55a95508a67adc5231be",
            'label' => "Energyno 045",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "46",
            'token' => "61b6dc1d82bf808b13e52933f70cb107",
            'label' => "Energyno 046",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "47",
            'token' => "b28a819ed371dfadb289287822a67658",
            'label' => "Energyno 047",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "48",
            'token' => "eeb35c377c2681af454927d8f60fc21c",
            'label' => "Energyno 048",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "49",
            'token' => "7c7fbd5122d8dcb6513ab1ae1855a386",
            'label' => "Energyno 049",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('devices')->insert([
            'id' => "50",
            'token' => "b8d92c7559582b1f47178a6049ac65d8",
            'label' => "Energyno 050",
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now(),
        ]);*/
    }
}
