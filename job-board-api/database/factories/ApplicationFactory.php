<?php

namespace Database\Factories;

use App\Models\Application;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'resume_path' => 'resumes/sample_cv.pdf',
            'status' => fake()->randomElement(['applied', 'interviewing', 'accepted', 'rejected']),
            'is_paid' => fake()->boolean(20),
            'cover_letter' => fake()->paragraph(),
        ];
    }
}
