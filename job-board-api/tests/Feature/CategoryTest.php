<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_anyone_can_list_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function test_admin_can_create_a_category()
    {
        Role::create(['name' => 'admin', 'guard_name' => 'api']);

        $admin = User::factory()->create();
        $admin->assignRole('admin'); 

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/categories', [
                'name' => 'Backend Development'
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', [
            'name' => 'Backend Development',
            'slug' => 'backend-development'
        ]);
    }

    /** @test */
    public function test_non_admin_cannot_delete_category()
    {
        Role::create(['name' => 'candidate', 'guard_name' => 'api']);

        $user = User::factory()->create();
        $user->assignRole('candidate');

        $category = Category::factory()->create(['name' => 'Design']);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/categories/{$category->slug}");

        $response->assertStatus(403);
    }
}