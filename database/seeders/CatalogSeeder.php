<?php

namespace Database\Seeders;

use App\Models\Filling;
use App\Models\Grind;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            'Ethiopian Yirgacheffe',
            'Colombia Huila',
            'Espresso Blend',
            'House Blend',
            'Decaf Blend',
            'Specialty Single Origin',
            'Kenya AA',
            'Rwanda Bourbon',
            'Premium Roast',
        ];
        foreach ($products as $name) {
            Product::firstOrCreate(['name' => $name], ['is_active' => true]);
        }

        $fillings = ['Sample', '250 g', '1 kg'];
        foreach ($fillings as $name) {
            Filling::firstOrCreate(['name' => $name], ['is_active' => true]);
        }

        $grinds = ['حب', 'مطحون'];
        foreach ($grinds as $name) {
            Grind::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
