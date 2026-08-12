<?php

namespace Tests\Feature;

use App\Models\LearningTrack;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LearningTest extends TestCase
{
    use RefreshDatabase;

    private function createTrack(): LearningTrack
    {
        return LearningTrack::create([
            'title'         => 'Full Stack Development',
            'slug'          => 'full-stack',
            'description'   => 'Learn full stack web development.',
            'display_order' => 1,
        ]);
    }

    private function createSubject(LearningTrack $track): Subject
    {
        return Subject::create([
            'learning_track_id' => $track->id,
            'title'             => 'HTML & CSS',
            'slug'              => 'html-css',
            'description'       => 'Web fundamentals.',
            'display_order'     => 1,
        ]);
    }

    private function createTopic(Subject $subject): Topic
    {
        return Topic::create([
            'subject_id'    => $subject->id,
            'title'         => 'Intro to HTML',
            'slug'          => 'intro-html',
            'description'   => 'Basic HTML tags.',
            'display_order' => 1,
        ]);
    }

    private function createLesson(Topic $topic): Lesson
    {
        return Lesson::create([
            'topic_id'          => $topic->id,
            'title'             => 'What is HTML?',
            'content'           => 'HTML stands for HyperText Markup Language.',
            'estimated_minutes' => 5,
            'display_order'     => 1,
        ]);
    }

    // ------------------------------------------------------------------ unauthenticated

    public function test_learning_endpoints_require_authentication(): void
    {
        $this->getJson('/api/v1/tracks')->assertStatus(401);
    }

    // ------------------------------------------------------------------ tracks

    public function test_tracks_index_returns_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $this->createTrack();

        $response = $this->getJson('/api/v1/tracks');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    public function test_track_show_returns_single_track(): void
    {
        $user  = User::factory()->create();
        $track = $this->createTrack();
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/tracks/{$track->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', 'full-stack');
    }

    // ------------------------------------------------------------------ subjects

    public function test_track_subjects_returns_list(): void
    {
        $user    = User::factory()->create();
        $track   = $this->createTrack();
        $this->createSubject($track);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/tracks/{$track->id}/subjects");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    // ------------------------------------------------------------------ topics

    public function test_subject_topics_returns_list(): void
    {
        $user    = User::factory()->create();
        $track   = $this->createTrack();
        $subject = $this->createSubject($track);
        $this->createTopic($subject);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/subjects/{$subject->id}/topics");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    // ------------------------------------------------------------------ lessons

    public function test_topic_lessons_returns_list(): void
    {
        $user    = User::factory()->create();
        $track   = $this->createTrack();
        $subject = $this->createSubject($track);
        $topic   = $this->createTopic($subject);
        $this->createLesson($topic);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/topics/{$topic->id}/lessons");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_lesson_show_returns_lesson(): void
    {
        $user    = User::factory()->create();
        $track   = $this->createTrack();
        $subject = $this->createSubject($track);
        $topic   = $this->createTopic($subject);
        $lesson  = $this->createLesson($topic);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/lessons/{$lesson->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'What is HTML?');
    }
}
