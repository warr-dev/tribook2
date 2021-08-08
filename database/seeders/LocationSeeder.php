<?php

namespace Database\Seeders;

use App\Models\Locations;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Locations::truncate();
        $locations=[
            'Calapan Public Market'=>0,
            'C5 Road Calapan'=>4.7,
            'Masipit'=>3.7,
            'Tawiran'=>2.3,
            'Lalud'=>1.8,
            'Camilmil'=>1,
            'Sta Rosa Baco'=>19,
        ];
        foreach(array_keys($locations) as $key){
            Locations::create([
                'name'=>$key,
                'distance'=>$locations[$key]
            ]);
        }
    }
}
