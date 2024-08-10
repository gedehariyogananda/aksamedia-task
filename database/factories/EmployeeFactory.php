<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
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
        $keyUUIDDivision = [
            'c251492d-e2b7-4b69-a4da-f97c8469aea8',
            'c020cebe-a8a7-4ef2-8078-f973970cbde6',
            'db329fc3-e17b-4ce4-ae3b-e12ae1e360ee',
            '44fd6430-5eb7-4679-909e-47c2b7b67283',
            '4c1ea588-7916-4efc-a896-a33755dd08ea',
            '76276b40-2800-46e2-8184-7a24ead46381'
        ]; //hanya untuk dummy data!


        return [
            'id' => Str::uuid(),
            'name' => fake()->name(),
            'image' => 'pegawai.jpg',
            'phone' => fake()->phoneNumber(),
            'position' => fake()->jobTitle(),
            'division_id' => fake()->randomElement($keyUUIDDivision),
        ];
    }
}
