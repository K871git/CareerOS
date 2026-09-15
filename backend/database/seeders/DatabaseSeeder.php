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
        // ── 1. Foundation (no dependencies) ───────────────────────────────
        $this->call(SkillSeeder::class);
        $this->call(LearningTrackSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(TopicSeeder::class);
        $this->call(LessonSeeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(QuestionOptionSeeder::class);

        // ── 2. Practice MCQ seeders (depend on track/subject/topic) ───────
        $this->call(PhpPracticeSeeder::class);
        $this->call(TypeScriptPracticeSeeder::class);
        $this->call(AngularPracticeSeeder::class);
        $this->call(NodeJsPracticeSeeder::class);
        $this->call(HtmlPracticeSeeder::class);
        $this->call(CssPracticeSeeder::class);
        $this->call(ExpressPracticeSeeder::class);
        $this->call(MySqlPracticeSeeder::class);
        $this->call(PostgreSqlPracticeSeeder::class);

        // ── 3. Question seeders (depend on topics from practice seeders) ──
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
        $this->call(PythonJuniorQuestionsSeeder::class);
        $this->call(PythonIntermediateQuestionsSeeder::class);
        $this->call(PythonAdvancedQuestionsSeeder::class);

        // ── 4. Learning content seeders (add lessons + L4/L5 questions) ──
        $this->call(JsLearningSeeder::class);
        $this->call(ReactLearningSeeder::class);
        $this->call(PhpLearningSeeder::class);
        $this->call(PythonLearningSeeder::class);
        $this->call(TypeScriptLearningSeeder::class);
        $this->call(AngularLearningSeeder::class);
        $this->call(LaravelLearningSeeder::class);
        $this->call(NodeJsLearningSeeder::class);
        $this->call(ExpressLearningSeeder::class);
        $this->call(MySqlLearningSeeder::class);
        $this->call(SqlTheoryLearningSeeder::class);
        $this->call(PostgreSqlLearningSeeder::class);

        // ── 5. Coding problems (standalone, no FK dependencies) ──────────
        $this->call(CodingProblemsSeeder::class);

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')],
        );
    }
}
