<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('theory_area', 50)->nullable()->after('explanation');
            $table->tinyInteger('theory_level')->unsigned()->nullable()->after('theory_area');
            $table->index(['theory_area', 'theory_level'], 'questions_theory_area_level_index');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('questions_theory_area_level_index');
            $table->dropColumn(['theory_area', 'theory_level']);
        });
    }
};
