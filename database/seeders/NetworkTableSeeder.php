<?php

namespace Database\Seeders;

use App\Network;
use Illuminate\Database\Seeder;
use App\User;
use App\Business;
use App\NetworkFollower;

class NetworkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Network::factory()->count(50)
        ->has(NetworkFollower::factory()->count(6), 'networkFollower')
        ->for(User::factory(),'users')
        ->for(Business::factory(),'business')
        ->create();
    }
}
