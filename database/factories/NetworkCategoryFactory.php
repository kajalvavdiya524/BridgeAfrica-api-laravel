<?php

namespace Database\Factories;

use App\NetworkCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class NetworkCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NetworkCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' =>$this->faker->title(),
        ];
    }
}
