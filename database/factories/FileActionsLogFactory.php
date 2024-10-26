<?php

namespace Database\factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileActionsLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'file_id'        =>  $this->faker->numberBetween(0, 9223372036854775807),
			'user_id'        =>  $this->faker->numberBetween(0, 9223372036854775807),
			'action'         =>  $this->faker->randomElement(['UPLOAD', 'UPDATE', 'DELETE', 'MOVE', 'COPY', 'RENAME', 'CHECK_IN', 'CHECK_OUT']),
			'to_group'       =>  Group::all()->pluck('id')->random(),
			'old_file_name'  =>  $this->faker->text(255),
			'new_file_name'  =>  $this->faker->text(255),
		];
    }
}
