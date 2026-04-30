<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    User::factory()->create([
        'name' => 'Admin User',
        'email' => 'admin@gmail.com',
        'password' => Hash::make('12345678'),
        'role' => 'admin',
    ]);

    $employers = User::factory(5)->create(['role' => 'employer']);

    $candidates = User::factory(10)->create(['role' => 'candidate']);

    $categories = ['Programming', 'Design', 'Marketing', 'Writing', 'Sales'];
    foreach ($categories as $cat) {
        Category::create(['name' => $cat, 'slug' => str()->slug($cat)]);
    }

    $employers->each(function ($employer) {
        Job::factory(3)->create([
            'employer_id' => $employer->id,
            'category_id' => Category::inRandomOrder()->first()->id,
        ]);
    });

    $jobs = Job::all();
    $candidates->each(function ($candidate) use ($jobs) {
        Application::create([
            'candidate_id' => $candidate->id,
            'job_id' => $jobs->random()->id,
            'resume_path' => 'resumes/sample.pdf',
            'status' => 'applied',
        ]);
    });

    $users = User::all();

    foreach ($jobs as $job) {
        Comment::factory(rand(2, 5))->create([
            'job_id' => $job->id,
            'user_id' => $users->random()->id,
        ]);
    }
}
}
