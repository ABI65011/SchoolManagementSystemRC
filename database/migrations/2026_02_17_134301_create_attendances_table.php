<?php

use App\Helpers\AttendanceStatus;
use App\Helpers\CheckInMethod;
use App\Models\staff;
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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->date('attendance_date');
            $table->dateTime('check_in');
            $table->dateTime('check_out')->nullable();
            $table->enum('check_in_method', array_column(CheckInMethod::cases(), 'value'));
            $table->enum('check_out_method', array_column(CheckInMethod::cases(), 'value'));
            $table->string('check_in_location')->nullable();
            $table->string('check_out_location')->nullable();
            $table->enum('status', array_column(AttendanceStatus::cases(), 'value'));
            $table->integer('late_minutes')->nullable();
            $table->integer('early_departure_minutes')->nullable();
            $table->decimal('working_hours', 4, 2)->nullable();
            $table->boolean('is_holiday');
            $table->boolean('is_weekend');
            $table->decimal('overtime_hours', 4, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
