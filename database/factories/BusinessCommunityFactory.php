<?php

namespace Database\Factories;

use App\BusinessCommunity;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessCommunityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BusinessCommunity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'follower_type' => $this->faker->randomElement(['business', 'user', 'network']),
            'type' => $this->faker->randomElement(['follower', 'following']),
        ];
    }
}
