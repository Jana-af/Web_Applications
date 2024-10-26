<?php

namespace Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'group_name'  =>  $this->faker->text(255),
			'group_type'  =>  $this->faker->randomElement(['PUBLIC', 'PRIVATE', 'SHARED']),
        ];
    }
}
