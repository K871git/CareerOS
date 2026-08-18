<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theory_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('theory_area', 50);
            $table->unsignedTinyInteger('level');
            $table->unsignedTinyInteger('score');
            $table->boolean('passed');
            $table->timestamps();

            $table->unique(['user_id', 'theory_area', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theory_completions');
    }
};
