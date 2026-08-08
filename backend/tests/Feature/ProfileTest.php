<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(): array
    {
        return [
            'experience_level' => 'junior',
            'target_role'      => 'Full Stack Developer',
            'current_role'     => 'Student',
            'career_goal'      => 'Land my first dev job',
        ];
    }

    // ------------------------------------------------------------------ unauthenticated

    public function test_profile_endpoints_require_authentication(): void
    {
        $this->getJson('/api/v1/profile')->assertStatus(401);
        $this->putJson('/api/v1/profile', [])->assertStatus(401);
    }

    // ------------------------------------------------------------------ show

    public function test_show_returns_404_when_no_profile_exists(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/profile')->assertStatus(404);
    }

    public function test_show_returns_profile_when_it_exists(): void
    {
        $user = User::factory()->create();
        $user->profile()->create($this->validPayload() + ['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.target_role', 'Full Stack Developer');
    }

    // ------------------------------------------------------------------ update

    public function test_update_creates_profile_when_none_exists(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/profile', $this->validPayload());

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.target_role', 'Full Stack Developer');

        $this->assertDatabaseHas('user_profiles', [
            'user_id'     => $user->id,
            'target_role' => 'Full Stack Developer',
        ]);
    }

    public function test_update_modifies_existing_profile(): void
    {
        $user = User::factory()->create();
        $user->profile()->create($this->validPayload() + ['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/profile', array_merge(
            $this->validPayload(),
            ['target_role' => 'Backend Engineer']
        ));

        $response->assertStatus(200)
            ->assertJsonPath('data.target_role', 'Backend Engineer');
    }

    public function test_update_rejects_missing_required_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->putJson('/api/v1/profile', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['experience_level', 'target_role']);
    }
}
