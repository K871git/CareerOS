<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('consent_version', 20)->nullable()->after('avatar');
            $table->timestamp('consent_accepted_at')->nullable()->after('consent_version');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['consent_version', 'consent_accepted_at']);
        });
    }
};
