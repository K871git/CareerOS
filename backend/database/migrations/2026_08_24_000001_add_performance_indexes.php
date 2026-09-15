<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Composite indexes for assessment_attempts — covers user dashboard + topic history queries
        Schema::table('assessment_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'submitted_at'], 'idx_attempts_user_submitted');
            $table->index(['user_id', 'topic_id'],    'idx_attempts_user_topic');
        });

        // Composite index for user_progress — covers status-filtered progress queries
        Schema::table('user_progress', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_progress_user_status');
        });

        // Composite index for assessment_answers — covers per-attempt answer lookups
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->index(['attempt_id', 'question_id'], 'idx_answers_attempt_question');
        });

        // Index for level_completions — covers "show all passed levels for user" queries
        Schema::table('level_completions', function (Blueprint $table) {
            $table->index(['user_id', 'passed'], 'idx_level_completions_user_passed');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_attempts', function (Blueprint $table) {
            $table->dropIndex('idx_attempts_user_submitted');
            $table->dropIndex('idx_attempts_user_topic');
        });

        Schema::table('user_progress', function (Blueprint $table) {
            $table->dropIndex('idx_progress_user_status');
        });

        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropIndex('idx_answers_attempt_question');
        });

        Schema::table('level_completions', function (Blueprint $table) {
            $table->dropIndex('idx_level_completions_user_passed');
        });
    }
};
