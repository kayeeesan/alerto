<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            // Region I
            ['name' => 'Ilocos Norte', 'region_id' => 1],
            ['name' => 'Ilocos Sur', 'region_id' => 1],
            ['name' => 'La Union', 'region_id' => 1],
            ['name' => 'Pangasinan', 'region_id' => 1],

            // Region II
            ['name' => 'Batanes', 'region_id' => 2],
            ['name' => 'Cagayan', 'region_id' => 2],
            ['name' => 'Isabela', 'region_id' => 2],
            ['name' => 'Nueva Vizcaya', 'region_id' => 2],
            ['name' => 'Quirino', 'region_id' => 2],

            // Region III
            ['name' => 'Aurora', 'region_id' => 3],
            ['name' => 'Bataan', 'region_id' => 3],
            ['name' => 'Bulacan', 'region_id' => 3],
            ['name' => 'Nueva Ecija', 'region_id' => 3],
            ['name' => 'Pampanga', 'region_id' => 3],
            ['name' => 'Tarlac', 'region_id' => 3],
            ['name' => 'Zambales', 'region_id' => 3],

            // Region IV-A
            ['name' => 'Batangas', 'region_id' => 4],
            ['name' => 'Cavite', 'region_id' => 4],
            ['name' => 'Laguna', 'region_id' => 4],
            ['name' => 'Quezon', 'region_id' => 4],
            ['name' => 'Rizal', 'region_id' => 4],

            // MIMAROPA
            ['name' => 'Marinduque', 'region_id' => 5],
            ['name' => 'Occidental Mindoro', 'region_id' => 5],
            ['name' => 'Oriental Mindoro', 'region_id' => 5],
            ['name' => 'Palawan', 'region_id' => 5],
            ['name' => 'Romblon', 'region_id' => 5],

            // Region V
            ['name' => 'Albay', 'region_id' => 6],
            ['name' => 'Camarines Norte', 'region_id' => 6],
            ['name' => 'Camarines Sur', 'region_id' => 6],
            ['name' => 'Catanduanes', 'region_id' => 6],
            ['name' => 'Masbate', 'region_id' => 6],
            ['name' => 'Sorsogon', 'region_id' => 6],

            // Region VI
            ['name' => 'Aklan', 'region_id' => 7],
            ['name' => 'Antique', 'region_id' => 7],
            ['name' => 'Capiz', 'region_id' => 7],
            ['name' => 'Guimaras', 'region_id' => 7],
            ['name' => 'Iloilo', 'region_id' => 7],
            ['name' => 'Negros Occidental', 'region_id' => 7],

            // Region VII
            ['name' => 'Bohol', 'region_id' => 8],
            ['name' => 'Cebu', 'region_id' => 8],
            ['name' => 'Negros Oriental', 'region_id' => 8],
            ['name' => 'Siquijor', 'region_id' => 8],

            // Region VIII
            ['name' => 'Biliran', 'region_id' => 9],
            ['name' => 'Eastern Samar', 'region_id' => 9],
            ['name' => 'Leyte', 'region_id' => 9],
            ['name' => 'Northern Samar', 'region_id' => 9],
            ['name' => 'Samar', 'region_id' => 9],
            ['name' => 'Southern Leyte', 'region_id' => 9],

            // Region IX
            // ['name' => 'Zamboanga City', 'region_id' => 10], // 49
            ['name' => 'Zamboanga del Norte', 'region_id' => 10],//50 / 49
            ['name' => 'Zamboanga del Sur', 'region_id' => 10],
            ['name' => 'Zamboanga Sibugay', 'region_id' => 10],

            // Region X
            ['name' => 'Bukidnon', 'region_id' => 11],
            ['name' => 'Camiguin', 'region_id' => 11],
            ['name' => 'Lanao del Norte', 'region_id' => 11],
            ['name' => 'Misamis Occidental', 'region_id' => 11],
            ['name' => 'Misamis Oriental', 'region_id' => 11],

            // Region XI
            ['name' => 'Davao de Oro', 'region_id' => 12],
            ['name' => 'Davao del Norte', 'region_id' => 12],
            ['name' => 'Davao del Sur', 'region_id' => 12],
            ['name' => 'Davao Occidental', 'region_id' => 12],
            ['name' => 'Davao Oriental', 'region_id' => 12],

            // Region XII
            ['name' => 'Cotabato', 'region_id' => 13],
            ['name' => 'Sarangani', 'region_id' => 13],
            ['name' => 'South Cotabato', 'region_id' => 13],
            ['name' => 'Sultan Kudarat', 'region_id' => 13],

            // Region XIII
            ['name' => 'Agusan del Norte', 'region_id' => 14],
            ['name' => 'Agusan del Sur', 'region_id' => 14],
            ['name' => 'Dinagat Islands', 'region_id' => 14],
            ['name' => 'Surigao del Norte', 'region_id' => 14],
            ['name' => 'Surigao del Sur', 'region_id' => 14],

            // NCR - No provinces, usually cities (optional to leave blank or skip)
            // NCR - National Capital Region
            ['name' => 'Caloocan', 'region_id' => 15],
            ['name' => 'Las Piñas', 'region_id' => 15],
            ['name' => 'Makati', 'region_id' => 15],
            ['name' => 'Malabon', 'region_id' => 15],
            ['name' => 'Mandaluyong', 'region_id' => 15],
            ['name' => 'Manila', 'region_id' => 15],
            ['name' => 'Marikina', 'region_id' => 15],
            ['name' => 'Muntinlupa', 'region_id' => 15],
            ['name' => 'Navotas', 'region_id' => 15],
            ['name' => 'Parañaque', 'region_id' => 15],
            ['name' => 'Pasay', 'region_id' => 15],
            ['name' => 'Pasig', 'region_id' => 15],
            ['name' => 'Quezon City', 'region_id' => 15],
            ['name' => 'San Juan', 'region_id' => 15],
            ['name' => 'Taguig', 'region_id' => 15],
            ['name' => 'Valenzuela', 'region_id' => 15],
            ['name' => 'Pateros', 'region_id' => 15], // Only municipality


            // CAR
            ['name' => 'Abra', 'region_id' => 16],
            ['name' => 'Apayao', 'region_id' => 16],
            ['name' => 'Benguet', 'region_id' => 16],
            ['name' => 'Ifugao', 'region_id' => 16],
            ['name' => 'Kalinga', 'region_id' => 16],
            ['name' => 'Mountain Province', 'region_id' => 16],

            // BARMM
            ['name' => 'Basilan', 'region_id' => 17],
            ['name' => 'Lanao del Sur', 'region_id' => 17],
            ['name' => 'Maguindanao del Norte', 'region_id' => 17],
            ['name' => 'Maguindanao del Sur', 'region_id' => 17],
            ['name' => 'Sulu', 'region_id' => 17],
            ['name' => 'Tawi-Tawi', 'region_id' => 17],
        ];

        foreach ($provinces as $province) {
            Province::firstOrCreate($province);
        }
    }
}
