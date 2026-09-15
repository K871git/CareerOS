<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Extends otp_tokens.code from varchar(6) to varchar(255) so bcrypt hashes fit.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otp_tokens', function (Blueprint $table) {
            $table->string('code', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('otp_tokens', function (Blueprint $table) {
            $table->string('code', 6)->change();
        });
    }
};
