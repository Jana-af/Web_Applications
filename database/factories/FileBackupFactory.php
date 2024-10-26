<?php

namespace Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FileBackupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'file_id'   =>  $this->faker->numberBetween(0, 9223372036854775807),
			'file_url'  =>  $this->faker->text(255),
        ];
    }
}
