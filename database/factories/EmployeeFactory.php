<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $branch_id  = DB::table('branches')->inRandomOrder()->limit(1)->get()[0]->id;
        $role_id    = DB::table('roles')->inRandomOrder()->limit(1)->get()[0]->id;

        return [
            'no_employee'   => fake()->company(),
            'branch_id'     => $branch_id,
            'role_id'       => $role_id,
        ];
    }
}
