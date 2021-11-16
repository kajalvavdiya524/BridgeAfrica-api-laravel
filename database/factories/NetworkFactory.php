<?php

namespace Database\Factories;

use App\Network;
use App\Business;
use App\NetworkCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class NetworkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Network::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' =>$this->faker->name(),
            'business_id' => Business::factory(),
            'description' =>$this->faker->text(),
            'network_category_id' => NetworkCategory::factory(),
            'purpose' =>$this->faker->text(),
            'special_needs' =>$this->faker->text(),
            'image' => $this->faker->image(null,640,480,true),
            'address' =>$this->faker->address(),
            'country_id' =>$this->faker->numerify('#'),
            'email' =>$this->faker->email(),
            'primary_phone' =>$this->faker->numerify('###-###-###'),
            'secondary_phone' =>$this->faker->numerify('###-###-###'),
            'city' =>$this->faker->randomElement(['Buea', 'Yaounde', 'Douala', 'Limbe', 'Garoua', 'Bamenda', 'Adamawa', 'Bingo', 'Bonaberi'])
        ];
    }
}
