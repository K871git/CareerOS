<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('problem_id')->constrained('coding_problems')->cascadeOnDelete();
            $table->string('language', 20)->default('php');
            $table->text('code');
            $table->enum('status', ['accepted', 'wrong_answer', 'time_limit_exceeded', 'error', 'pending'])->default('pending');
            $table->unsignedSmallInteger('test_cases_passed')->default(0);
            $table->unsignedSmallInteger('test_cases_total')->default(0);
            $table->unsignedInteger('execution_time_ms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_submissions');
    }
};
