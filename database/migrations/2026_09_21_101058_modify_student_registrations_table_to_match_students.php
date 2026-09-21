<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('student_registration_subject');
        Schema::dropIfExists('student_registrations');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        Schema::create('student_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->date('birthday');
            $table->string('nrc_id');
            $table->foreignId('grade_id')->constrained()->onDelete('cascade');
            $table->string('guardian_name');
            $table->string('guardian_contact');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
        
        Schema::create('student_registration_subject', function (Blueprint $table) {
            $table->foreignId('student_registration_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->primary(['student_registration_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_registrations');
        Schema::dropIfExists('student_registration_subject');
    }
};
