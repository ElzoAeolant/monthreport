<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Devices>
 */
class DeviceFactory extends Factory
{
    protected $model = Device::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'label' => "ENERGYNO ", // secret
            'token' => Str::random(64),
            'status' => 'Operativo',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
