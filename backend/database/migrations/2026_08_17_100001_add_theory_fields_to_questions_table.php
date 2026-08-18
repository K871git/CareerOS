<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('topic_id')->nullable()->change();
            $table->string('theory_area', 50)->nullable()->after('explanation');
            $table->unsignedTinyInteger('theory_level')->nullable()->after('theory_area');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['theory_area', 'theory_level']);
            $table->unsignedBigInteger('topic_id')->nullable(false)->change();
        });
    }
};
