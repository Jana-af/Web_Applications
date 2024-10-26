<?php

namespace Database\factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'file_name'            =>  $this->faker->text(255),
			'file_url'             =>  $this->faker->text(255),
			'status'               =>  $this->faker->randomElement(['FREE', 'RESERVED']),
			'current_reserver_id'  =>  $this->faker->numberBetween(0, 9223372036854775807),
			'publisher_id'         =>  $this->faker->numberBetween(0, 9223372036854775807),
			'group_id'             =>  Group::all()->pluck('id')->random(),
			'is_added'             =>  $this->faker->numberBetween(-2147483648, 2147483647),
        ];
    }
}
