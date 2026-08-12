<?php

namespace Tests\Feature;

use App\Models\AssessmentAttempt;
use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class McqTest extends TestCase
{
    use RefreshDatabase;

    private function createTopic(): Topic
    {
        $track   = LearningTrack::create(['title' => 'Track', 'slug' => 'track', 'display_order' => 1]);
        $subject = Subject::create(['learning_track_id' => $track->id, 'title' => 'Subject', 'slug' => 'subject', 'display_order' => 1]);

        return Topic::create(['subject_id' => $subject->id, 'title' => 'Topic', 'slug' => 'topic', 'display_order' => 1]);
    }

    private function createMcqQuestion(Topic $topic): array
    {
        $question = Question::create([
            'topic_id'   => $topic->id,
            'type'       => 'MCQ',
            'difficulty' => 'Easy',
            'question'   => 'What does HTML stand for?',
            'explanation'=> 'HyperText Markup Language.',
        ]);

        $correct = QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'HyperText Markup Language',
            'is_correct'  => true,
        ]);
        $wrong = QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'High Transfer Machine Language',
            'is_correct'  => false,
        ]);

        return [$question, $correct, $wrong];
    }

    // ------------------------------------------------------------------ questions index

    public function test_topic_questions_returns_list(): void
    {
        $user  = User::factory()->create();
        $topic = $this->createTopic();
        $this->createMcqQuestion($topic);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/topics/{$topic->id}/questions");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    // ------------------------------------------------------------------ submit

    public function test_submit_assessment_returns_attempt_with_score(): void
    {
        $user  = User::factory()->create();
        $topic = $this->createTopic();
        [$question, $correct] = $this->createMcqQuestion($topic);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/assessments/submit', [
            'answers' => [
                ['question_id' => $question->id, 'selected_option_id' => $correct->id],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.score', 1)
            ->assertJsonPath('data.total_questions', 1);
    }

    public function test_submit_wrong_answer_scores_zero(): void
    {
        $user  = User::factory()->create();
        $topic = $this->createTopic();
        [, , $wrong] = $this->createMcqQuestion($topic);
        $question = $wrong->question;
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/assessments/submit', [
            'answers' => [
                ['question_id' => $question->id, 'selected_option_id' => $wrong->id],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.score', 0);
    }

    public function test_submit_with_missing_answers_returns_422(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/assessments/submit', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['answers']);
    }

    // ------------------------------------------------------------------ result

    public function test_result_returns_own_attempt(): void
    {
        $user    = User::factory()->create();
        $attempt = AssessmentAttempt::create([
            'user_id'         => $user->id,
            'score'           => 1,
            'total_questions' => 1,
            'started_at'      => now(),
            'submitted_at'    => now(),
        ]);
        Sanctum::actingAs($user);

        $this->getJson("/api/v1/assessments/{$attempt->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.score', 1);
    }

    public function test_result_returns_403_for_other_users_attempt(): void
    {
        $owner   = User::factory()->create();
        $other   = User::factory()->create();
        $attempt = AssessmentAttempt::create([
            'user_id'         => $owner->id,
            'score'           => 1,
            'total_questions' => 1,
            'started_at'      => now(),
            'submitted_at'    => now(),
        ]);
        Sanctum::actingAs($other);

        $this->getJson("/api/v1/assessments/{$attempt->id}")
            ->assertStatus(403);
    }
}
