<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::insert([
            [
                'name' => 'Orlando',
                'state' => 'FL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Miami',
                'state' => 'FL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sarasota',
                'state' => 'FL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Clearwater',
                'state' => 'FL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Windermere',
                'state' => 'FL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
