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
        Schema::create('student_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->date('birthday');
            $table->string('nrc_id');
            $table->foreignId('grade_id')->constrained()->onDelete('cascade');
            $table->string('guardian_name');
            $table->string('guardian_contact');
            $table->enum('registration_status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->timestamp('registration_confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_registrations');
    }
};
