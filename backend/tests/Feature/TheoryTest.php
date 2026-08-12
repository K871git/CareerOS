<?php

namespace Tests\Feature;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\TheoryAnswer;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TheoryTest extends TestCase
{
    use RefreshDatabase;

    private function createTopic(): Topic
    {
        $track   = LearningTrack::create(['title' => 'Track', 'slug' => 'track', 'display_order' => 1]);
        $subject = Subject::create(['learning_track_id' => $track->id, 'title' => 'Subject', 'slug' => 'subject', 'display_order' => 1]);

        return Topic::create(['subject_id' => $subject->id, 'title' => 'Topic', 'slug' => 'topic', 'display_order' => 1]);
    }

    private function createTheoryQuestion(Topic $topic): Question
    {
        return Question::create([
            'topic_id'    => $topic->id,
            'type'        => 'THEORY',
            'difficulty'  => 'Medium',
            'question'    => 'Explain the box model in CSS.',
            'explanation' => 'Content, padding, border, margin.',
        ]);
    }

    private function createMcqQuestion(Topic $topic): Question
    {
        $question = Question::create([
            'topic_id'    => $topic->id,
            'type'        => 'MCQ',
            'difficulty'  => 'Easy',
            'question'    => 'What is HTML?',
            'explanation' => 'Markup language.',
        ]);
        QuestionOption::create(['question_id' => $question->id, 'option_text' => 'A', 'is_correct' => true]);

        return $question;
    }

    private string $validAnswer = 'The CSS box model wraps every HTML element in a box consisting of content, padding, border, and margin areas.';

    // ------------------------------------------------------------------ index

    public function test_theory_questions_index_returns_only_theory_type(): void
    {
        $user  = User::factory()->create();
        $topic = $this->createTopic();
        $this->createTheoryQuestion($topic);
        $this->createMcqQuestion($topic);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/topics/{$topic->id}/theory-questions");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    // ------------------------------------------------------------------ submit

    public function test_submit_theory_answer_creates_record(): void
    {
        $user     = User::factory()->create();
        $topic    = $this->createTopic();
        $question = $this->createTheoryQuestion($topic);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/theory-questions/{$question->id}/submit", [
            'answer' => $this->validAnswer,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'pending_review');

        $this->assertDatabaseHas('theory_answers', [
            'user_id'     => $user->id,
            'question_id' => $question->id,
        ]);
    }

    public function test_resubmit_updates_existing_answer(): void
    {
        $user     = User::factory()->create();
        $topic    = $this->createTopic();
        $question = $this->createTheoryQuestion($topic);
        Sanctum::actingAs($user);

        $this->postJson("/api/v1/theory-questions/{$question->id}/submit", ['answer' => $this->validAnswer]);
        $this->postJson("/api/v1/theory-questions/{$question->id}/submit", ['answer' => $this->validAnswer . ' (revised)']);

        $this->assertDatabaseCount('theory_answers', 1);
    }

    public function test_submit_to_mcq_question_returns_422(): void
    {
        $user     = User::factory()->create();
        $topic    = $this->createTopic();
        $question = $this->createMcqQuestion($topic);
        Sanctum::actingAs($user);

        $this->postJson("/api/v1/theory-questions/{$question->id}/submit", [
            'answer' => $this->validAnswer,
        ])->assertStatus(422);
    }

    public function test_submit_short_answer_returns_422(): void
    {
        $user     = User::factory()->create();
        $topic    = $this->createTopic();
        $question = $this->createTheoryQuestion($topic);
        Sanctum::actingAs($user);

        $this->postJson("/api/v1/theory-questions/{$question->id}/submit", [
            'answer' => 'Too short.',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['answer']);
    }

    // ------------------------------------------------------------------ show

    public function test_show_own_answer_returns_200(): void
    {
        $user     = User::factory()->create();
        $topic    = $this->createTopic();
        $question = $this->createTheoryQuestion($topic);
        $answer   = TheoryAnswer::create([
            'user_id'     => $user->id,
            'question_id' => $question->id,
            'answer'      => $this->validAnswer,
            'status'      => 'pending_review',
        ]);
        Sanctum::actingAs($user);

        $this->getJson("/api/v1/theory-answers/{$answer->id}")
            ->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_show_other_users_answer_returns_403(): void
    {
        $owner    = User::factory()->create();
        $other    = User::factory()->create();
        $topic    = $this->createTopic();
        $question = $this->createTheoryQuestion($topic);
        $answer   = TheoryAnswer::create([
            'user_id'     => $owner->id,
            'question_id' => $question->id,
            'answer'      => $this->validAnswer,
            'status'      => 'pending_review',
        ]);
        Sanctum::actingAs($other);

        $this->getJson("/api/v1/theory-answers/{$answer->id}")
            ->assertStatus(403);
    }
}
