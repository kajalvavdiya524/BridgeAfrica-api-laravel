<?php

namespace Database\Factories;

use App\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' =>$this->faker->name(),
            'about_business' =>$this->faker->text(),
            'lat' => $this->faker->latitude(),
            'lng' =>$this->faker->longitude(),
            'logo_path' => $this->faker->image(null,640,480,true),
            'cover_image' => $this->faker->image(null,640,480,true),
            'location_description' =>$this->faker->text()
        ];
    }
}
