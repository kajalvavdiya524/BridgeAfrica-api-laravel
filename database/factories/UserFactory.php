<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'verified_at' => now(),
            'country_id' => $this->faker->numerify('#'),
            'city' => $this->faker->randomElement(['Buea', 'Yaounde']),
            'phone' => $this->faker->numerify('##########'),
            // 'lat' => $this->faker->latitude(),
            // 'lng' =>$this->faker->longitude(),
            'profile_picture' => $this->faker->image(null,640,480,true),
            'cover_picture' => $this->faker->image(null,640,480,true),
            'profession' => $this->faker->jobTitle(),
            'password' => 'password',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

