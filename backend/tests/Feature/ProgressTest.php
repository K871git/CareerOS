<?php

namespace Tests\Feature;

use App\Models\LearningTrack;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProgressTest extends TestCase
{
    use RefreshDatabase;

    private function createLesson(): array
    {
        $track   = LearningTrack::create(['title' => 'Track', 'slug' => 'track', 'display_order' => 1]);
        $subject = Subject::create(['learning_track_id' => $track->id, 'title' => 'Subject', 'slug' => 'subject', 'display_order' => 1]);
        $topic   = Topic::create(['subject_id' => $subject->id, 'title' => 'Topic', 'slug' => 'topic', 'display_order' => 1]);
        $lesson  = Lesson::create(['topic_id' => $topic->id, 'title' => 'Lesson One', 'display_order' => 1]);

        return [$track, $lesson];
    }

    // ------------------------------------------------------------------ unauthenticated

    public function test_progress_endpoints_require_authentication(): void
    {
        $this->getJson('/api/v1/progress')->assertStatus(401);
        $this->getJson('/api/v1/activity/recent')->assertStatus(401);
    }

    // ------------------------------------------------------------------ progress index

    public function test_progress_index_returns_summary_and_tracks(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/progress');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['summary', 'tracks']]);
    }

    public function test_progress_reflects_completed_lessons(): void
    {
        $user = User::factory()->create();
        [$track, $lesson] = $this->createLesson();

        UserProgress::create([
            'user_id'      => $user->id,
            'lesson_id'    => $lesson->id,
            'status'       => 'COMPLETED',
            'completed_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/progress');

        $response->assertStatus(200)
            ->assertJsonPath('data.summary.completed_lessons', 1);
    }

    // ------------------------------------------------------------------ complete lesson

    public function test_complete_lesson_creates_progress_record(): void
    {
        $user = User::factory()->create();
        [, $lesson] = $this->createLesson();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/lessons/{$lesson->id}/complete");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'COMPLETED');

        $this->assertDatabaseHas('user_progress', [
            'user_id'   => $user->id,
            'lesson_id' => $lesson->id,
            'status'    => 'COMPLETED',
        ]);
    }

    public function test_complete_lesson_is_idempotent(): void
    {
        $user = User::factory()->create();
        [, $lesson] = $this->createLesson();
        Sanctum::actingAs($user);

        $this->postJson("/api/v1/lessons/{$lesson->id}/complete");
        $this->postJson("/api/v1/lessons/{$lesson->id}/complete");

        $this->assertDatabaseCount('user_progress', 1);
    }

    // ------------------------------------------------------------------ recent activity

    public function test_recent_activity_returns_completed_lessons(): void
    {
        $user = User::factory()->create();
        [, $lesson] = $this->createLesson();

        UserProgress::create([
            'user_id'      => $user->id,
            'lesson_id'    => $lesson->id,
            'status'       => 'COMPLETED',
            'completed_at' => now(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/activity/recent');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_recent_activity_is_empty_for_new_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/activity/recent')
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    // ------------------------------------------------------------------ track progress

    public function test_track_progress_returns_nested_structure(): void
    {
        $user = User::factory()->create();
        [$track] = $this->createLesson();
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/tracks/{$track->id}/progress");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['track', 'subjects']]);
    }
}
