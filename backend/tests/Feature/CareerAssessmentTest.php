<?php

namespace Tests\Feature;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CareerAssessmentTest extends TestCase
{
    use RefreshDatabase;

    private function createSkill(string $name = 'PHP'): Skill
    {
        return Skill::create([
            'name'     => $name,
            'slug'     => strtolower($name),
            'category' => 'backend',
        ]);
    }

    private function validPayload(int $skillId): array
    {
        return [
            'target_role' => 'Backend Developer',
            'skills'      => [
                ['skill_id' => $skillId, 'level' => 'beginner', 'score' => 40],
            ],
        ];
    }

    // ------------------------------------------------------------------ unauthenticated

    public function test_career_assessment_endpoints_require_authentication(): void
    {
        $this->getJson('/api/v1/career-assessment')->assertStatus(401);
        $this->postJson('/api/v1/career-assessment', [])->assertStatus(401);
    }

    // ------------------------------------------------------------------ show

    public function test_show_returns_empty_data_for_new_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/career-assessment');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(0, 'data.skills');
    }

    // ------------------------------------------------------------------ store

    public function test_store_saves_target_role_and_skills(): void
    {
        $user  = User::factory()->create();
        $skill = $this->createSkill();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/career-assessment', $this->validPayload($skill->id));

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.target_role', 'Backend Developer')
            ->assertJsonCount(1, 'data.skills');

        $this->assertDatabaseHas('user_skills', [
            'user_id'  => $user->id,
            'skill_id' => $skill->id,
            'score'    => 40,
        ]);
    }

    public function test_store_rejects_missing_required_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/career-assessment', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['target_role', 'skills']);
    }

    public function test_store_rejects_invalid_skill_id(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/career-assessment', [
            'target_role' => 'Developer',
            'skills'      => [['skill_id' => 9999, 'level' => 'beginner', 'score' => 50]],
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['skills.0.skill_id']);
    }

    // ------------------------------------------------------------------ update

    public function test_update_replaces_skill_scores(): void
    {
        $user  = User::factory()->create();
        $skill = $this->createSkill();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/career-assessment', $this->validPayload($skill->id));

        $response = $this->putJson('/api/v1/career-assessment', [
            'target_role' => 'Senior Backend Developer',
            'skills'      => [['skill_id' => $skill->id, 'level' => 'intermediate', 'score' => 70]],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.target_role', 'Senior Backend Developer');

        $this->assertDatabaseHas('user_skills', [
            'user_id'  => $user->id,
            'skill_id' => $skill->id,
            'score'    => 70,
        ]);
    }
}
