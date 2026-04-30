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
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $employerRole = Role::create(['name' => 'employer', 'guard_name' => 'api']);
        $candidateRole = Role::create(['name' => 'candidate', 'guard_name' => 'api']);

        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);
        $admin->assignRole($adminRole);

        $candidate = User::factory()->create([
            'name' => 'Candidate User',
            'email' => 'candidate@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'candidate',
        ]);
        $candidate->assignRole($candidateRole);

        $employer = User::factory()->create([
            'name' => 'Employer User',
            'email' => 'employer@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'employer',
        ]);
        $employer->assignRole($employerRole);


        $employers = User::factory(5)->create(['role' => 'employer']);
        $employers->each(fn($user) => $user->assignRole($employerRole));

        $candidates = User::factory(10)->create(['role' => 'candidate']);
        $candidates->each(fn($user) => $user->assignRole($candidateRole));

        $categoriesNames = ['Programming', 'Design', 'Marketing', 'Writing', 'Sales'];
        foreach ($categoriesNames as $name)
        {
            Category::create(['name' => $name, 'slug' => str()->slug($name)]);
        }

        $employers->each(function ($emp)
        {
            Job::factory(3)->create([
                'employer_id' => $emp->id,
                'category_id' => Category::inRandomOrder()->first()->id,
            ]);
        });

        $jobs = Job::all();
        $candidates->each(function ($candidate) use ($jobs)
        {
            Application::create([
                'candidate_id' => $candidate->id,
                'job_id' => $jobs->random()->id,
                'resume_path' => 'resumes/sample.pdf',
                'status' => 'applied',
            ]);
        });

        $users = User::all();

        foreach ($jobs as $job)
        {
            Comment::factory(rand(2, 5))->create([
                'job_id' => $job->id,
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
