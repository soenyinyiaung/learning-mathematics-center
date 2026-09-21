<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['registration_status', 'registration_confirmed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->enum('registration_status', ['pending', 'confirmed', 'rejected'])->default('pending')->after('status');
            $table->timestamp('registration_confirmed_at')->nullable()->after('registration_status');
        });
    }
};
