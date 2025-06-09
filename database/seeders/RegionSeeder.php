<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;


class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $regions = [
            ['name' => 'Region I - Ilocos Region'],
            ['name' => 'Region II - Cagayan Valley'],
            ['name' => 'Region III - Central Luzon'],
            ['name' => 'Region IV-A - CALABARZON'],
            ['name' => 'MIMAROPA Region'],
            ['name' => 'Region V - Bicol Region'],
            ['name' => 'Region VI - Western Visayas'],
            ['name' => 'Region VII - Central Visayas'],
            ['name' => 'Region VIII - Eastern Visayas'],
            ['name' => 'Region IX - Zamboanga Peninsula'],
            ['name' => 'Region X - Northern Mindanao'],
            ['name' => 'Region XI - Davao Region'],
            ['name' => 'Region XII - SOCCSKSARGEN'],
            ['name' => 'Region XIII - Caraga'],
            ['name' => 'NCR - National Capital Region'],
            ['name' => 'CAR - Cordillera Administrative Region'],
            ['name' => 'BARMM - Bangsamoro Autonomous Region in Muslim Mindanao'],
        ];

        foreach ($regions as $region) {
            Region::firstOrCreate(['name' => $region['name']]);
        }
    }
}
