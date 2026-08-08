<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SkillSeeder::class);
        $this->call(PhpPracticeSeeder::class);
        $this->call(PhpJuniorQuestionsSeeder::class);
        $this->call(PhpIntermediateQuestionsSeeder::class);
        $this->call(PhpAdvancedQuestionsSeeder::class);
        $this->call(JsJuniorQuestionsSeeder::class);
        $this->call(JsIntermediateQuestionsSeeder::class);
        $this->call(JsAdvancedQuestionsSeeder::class);
        $this->call(LaravelJuniorQuestionsSeeder::class);
        $this->call(LaravelIntermediateQuestionsSeeder::class);
        $this->call(LaravelAdvancedQuestionsSeeder::class);
        $this->call(ReactJuniorQuestionsSeeder::class);
        $this->call(ReactIntermediateQuestionsSeeder::class);
        $this->call(ReactAdvancedQuestionsSeeder::class);
        $this->call(LearningTrackSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(TopicSeeder::class);
        $this->call(LessonSeeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(QuestionOptionSeeder::class);
        $this->call(TheoryQuestionSeeder::class);

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')],
        );
    }
}
