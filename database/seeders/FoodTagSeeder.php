<?php

namespace Database\Seeders;

use App\Models\FoodTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = ['veg', 'vegan', 'non-veg', 'contains-peanuts', 'gluten-free', 'contains-wheat'];

        foreach ($tags as $tag){
            FoodTag::updateOrCreate([
               'tag' => $tag,
            ],[

            ]);
        }
    }
}
