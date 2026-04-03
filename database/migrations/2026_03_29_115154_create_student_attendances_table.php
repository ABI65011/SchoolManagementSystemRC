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
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->date('attendance_date');
            $table->enum('sponsorship', ['government', 'private'])->default('private')
                ->comment('UPE/USE government sponsorship vs private');
            $table->enum('status', ['present', 'absent', 'late', 'excused', 'holiday'])
                ->default('present');
            $table->time('check_in_time')->nullable();
            $table->integer('late_minutes')->default(0);
            $table->time('check_out_time')->nullable();
            $table->integer('early_departure_minutes')->default(0);
            $table->string('absence_reason')->nullable();
            $table->text('absence_notes')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->integer('physical_headcount')->nullable()
                ->comment('Manual count from class register verification');
            $table->foreignId('recorded_by')->constrained('users');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'attendance_date']);
            $table->index(['class_id', 'attendance_date']);
            $table->index(['sponsorship', 'status']);
            $table->index('attendance_date');
        });


        Schema::create('attendance_discrepancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained();
            $table->date('date');
            $table->integer('physical_count');
            $table->integer('system_count');
            $table->integer('difference');
            $table->text('notes')->nullable();
            $table->foreignId('reported_by')->constrained('users');
            $table->boolean('resolved')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_discrepancies');
        Schema::dropIfExists('student_attendances');
    }
};
