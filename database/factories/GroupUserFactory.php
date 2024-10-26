<?php

namespace Database\factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'user_id'      =>  User::all()->pluck('id')->random(),
			'group_id'     =>  Group::all()->pluck('id')->random(),
			'is_owner'     =>  $this->faker->numberBetween(-128, 127),
			'is_accepted'  =>  $this->faker->numberBetween(-2147483648, 2147483647),
        ];
    }
}
