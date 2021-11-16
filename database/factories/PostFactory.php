<?php

namespace Database\Factories;

use App\Business;
use App\Post;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'content' => $this->faker->text(),
            'user_id' => $this->faker->numerify('#'),
            'business_id' => $this->faker->numerify('#'),
            'type' => $this->faker->randomElement(['media', 'picture']),
            'visit' => $this->faker->numerify('#'),
            'network_id' => $this->faker->numerify('#'),
        ];
    }
}
