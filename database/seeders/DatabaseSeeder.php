<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (\App\Models\Pokja::count() === 0) {
            $this->call([
                PokjaSeeder::class,
            ]);
        }

        if (\App\Models\Setting::count() === 0) {
            $this->call([
                SettingSeeder::class,
            ]);
        }

        User::firstOrCreate([
            'email' => 'verifikator@example.com',
        ], [
            'name' => 'Verifikator Status',
            'password' => Hash::make('password'),
            'role' => 'verifikator',
        ]);

        User::firstOrCreate([
            'email' => 'firchun025@gmail.com',
        ], [
            'name' => 'firmansyah diana',
            'password' => Hash::make('password'),
            'role' => 'it',
            'pokja_id' => 16,
        ]);
    }
}
