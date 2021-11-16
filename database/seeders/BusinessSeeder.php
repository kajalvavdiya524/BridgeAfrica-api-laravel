<?php

namespace Database\Seeders;

use App\Business;
use App\BusinessCommunity;
use App\User;
use App\BusinessOpenHours;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Business::factory()
            ->count(10)
            ->has(BusinessCommunity::factory()->count(6), 'businesscommunity')
            ->has(BusinessOpenHours::factory()->count(6), 'businessOpenHours')
            ->for(User::factory(),'user')
            ->create();


    }
}
