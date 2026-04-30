<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Job;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_anyone_can_view_all_jobs()
    {
        Job::factory()->count(5)->create();

        $response = $this->getJson('/api/jobs');

        $response->assertStatus(200)
            ->assertJsonCount(5);
    }

    /** @test */
    public function test_employer_can_create_a_job()
    {
        Role::create(['name' => 'employer', 'guard_name' => 'api']);
        $employer = User::factory()->create();
        $employer->assignRole('employer');

        $category = Category::factory()->create();

        $response = $this->actingAs($employer, 'sanctum')
            ->postJson('/api/jobs', [
                'title' => 'Laravel Developer',
                'description' => 'A very long description for the job post to pass validation.',
                'responsibilities' => 'Coding and testing',
                'requirements' => '3 years of experience',
                'skills' => 'PHP, Laravel',
                'work_type' => 'remote',
                'location' => 'Cairo',
                'salary_min' => 2000,
                'salary_max' => 5000,
                'category_id' => $category->id,
                'deadline' => now()->addMonth()->format('Y-m-d'),
            ]);


        $response->assertStatus(201);
    }

    /** @test */
    public function test_candidate_cannot_create_a_job()
    {
        Role::create(['name' => 'candidate', 'guard_name' => 'api']);
        $candidate = User::factory()->create();
        $candidate->assignRole('candidate');
        $response = $this->actingAs($candidate, 'sanctum')
            ->postJson('/api/jobs', [
                'title' => 'Hacker Job'
            ]);

        $response->assertStatus(403);
    }
}
