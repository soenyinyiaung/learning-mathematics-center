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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->date('voucher_date');
            $table->enum('customer_type', ['general', 'student']);
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('student_id_number')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->json('items');
            $table->timestamps();
            
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
