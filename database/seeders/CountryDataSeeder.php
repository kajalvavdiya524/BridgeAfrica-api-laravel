<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Country;


class CountryDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("database/seeders/Country-data.json");
        $data = json_decode($json);
        foreach ($data as $key => $cat) {
            $country = Country::create([
                'name' => $key
            ]);
            foreach ($cat as $key => $c) {
                foreach ($c as $key => $g) {
                    $region = $country->region()->create([
                        'name' => $key
                    ]);
                    foreach ($g as $key =>  $gd) {
                        foreach ($gd as $key =>  $gf) {
                            $division = $region->division()->create([
                                'name' => $key,
                            ]);
                            foreach ($gf as $key =>  $gh) {
                                foreach ($gh as $key =>  $go) {
                                    $council = $division->council()->create([
                                        'name' => $key
                                    ]);
                                    foreach ($go as $key =>  $gw) {
                                        if(sizeof($gw) == 3 ){
                                            $neigborhood = $council->neighborhood()->create([
                                                'name' => $gw['0'],
                                                'lat' => $gw['1'],
                                                'lng' => $gw['2']
                                            ]);
                                        }
                                        else {
                                            $neigborhood = $council->neighborhood()->create([
                                                'name' => $gw['0'],
                                                'lat' => null,
                                                'lng' => null
                                            ]);
                                        }
                                        
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

