<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'title' => fake()->jobTitle(),
        'description' => fake()->paragraphs(3, true),
        'responsibilities' => fake()->sentence(),
        'requirements' => fake()->sentence(),
        'skills' => 'PHP, Laravel, Vue.js',
        'work_type' => fake()->randomElement(['remote', 'onsite', 'hybrid']),
        'location' => fake()->city(),
        'salary_min' => fake()->numberBetween(1000, 3000),
        'salary_max' => fake()->numberBetween(4000, 8000),
        'status' => 'approved',
        'deadline' => now()->addMonth(),
        'employer_id' => User::factory(), 
        'category_id' => Category::factory(),
    ];
}
}
