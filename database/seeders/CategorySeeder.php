<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Sembako', 'Minuman', 'Makanan Ringan', 'Kebutuhan Rumah Tangga'];
            foreach ($categories as $name) {
                Category::create(['name' => $name]);
            }
    }
}