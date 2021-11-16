<?php

namespace Database\Factories;

use App\NetworkFollower;
use Illuminate\Database\Eloquent\Factories\Factory;

class NetworkFollowerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NetworkFollower::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'follower_type' => $this->faker->randomElement(['business', 'user', 'network']),
            'follower_id' => $this->faker->numerify('#'),
        ];
    }
}
