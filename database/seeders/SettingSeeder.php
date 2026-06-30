<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'hospital_name' => 'RSUD Merauke',
            'target_date' => null,
            'is_pendidikan' => false,
            'prognas_full' => true,
            'calc_mode' => 'bobot',
        ]);
    }
}
