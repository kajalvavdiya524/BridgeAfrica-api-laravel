<?php

namespace Database\Factories;

use App\UserFollower;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFollowerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserFollower::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'follower_type' => $this->faker->randomElement(['business', 'user', 'network']),
        ];
    }
}
