<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('problem_test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('problem_id')->constrained('coding_problems')->cascadeOnDelete();
            $table->text('input');
            $table->text('expected_output');
            $table->boolean('is_hidden')->default(false);
            $table->unsignedSmallInteger('order')->default(0);
            $table->string('label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_test_cases');
    }
};
