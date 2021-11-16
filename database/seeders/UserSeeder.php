<?php

namespace Database\Seeders;

use App\Business;
use App\UserFollower;
use Illuminate\Database\Seeder;
use App\User;
use App\Network;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->count(2)
            ->has(Business::factory()->count(1), 'business')
            // ->has(Network::factory()->count(10), 'networks')
            ->has(UserFollower::factory()->count(12), 'userfollower')
            ->create();
            User::factory()
            ->count(3)
            ->has(UserFollower::factory()->count(5), 'userfollower')
            ->create();
            User::factory()
            ->count(3)
            ->has(UserFollower::factory()->count(3), 'userfollower')
            ->create();
    }
}
