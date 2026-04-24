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
        Schema::create('student_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->dateTime('departure_time');
            $table->dateTime('expected_return_time');
            $table->dateTime('actual_return_time')->nullable();
            $table->string('destination');
            $table->string('reason');
            $table->foreignId('authorized_by')->constrained('users');
            $table->foreignId('signed_out_by')->nullable()->constrained('users');
            $table->foreignId('signed_in_by')->nullable()->constrained('users');
            $table->enum('status', ['pending', 'approved', 'denied', 'active', 'returned'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_leaves');
    }
};
