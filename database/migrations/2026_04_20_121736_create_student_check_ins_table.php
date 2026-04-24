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
        Schema::create('student_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->date('check_in_date');
            $table->time('check_in_time');
            $table->foreignId('entered_by')->constrained('users');
            $table->timestamps();
            $table->unique(['student_id', 'check_in_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_check_ins');
    }
};
