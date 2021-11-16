<?php

namespace Database\Factories;

use App\BusinessOpenHours;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessOpenHoursFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BusinessOpenHours::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'day' =>$this->faker->dayOfWeek(),
            'opening_time'=>$this->faker->time(),
            'closing_time' =>$this->faker->time()
        ];
    }
}
