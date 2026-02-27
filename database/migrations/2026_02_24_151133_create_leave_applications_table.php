<?php

use App\Helpers\LeaveStatus;
use App\Helpers\LeaveType;
use App\Helpers\ReplacementStatus;
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
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('supervisor_id')->nullable()->constrained('staff')->onDelete('cascade');
            $table->enum("type", array_column(LeaveType::cases(), 'value'));
            $table->string('other_reason')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('study_days_note')->nullable();
            $table->foreignId('replacement_employee_id')->nullable()->constrained('staff')->onDelete('cascade');
            $table->enum('replacement_status', array_column(ReplacementStatus::cases(), 'value'))->default(ReplacementStatus::Pending->value);
            $table->enum("status", array_column(LeaveStatus::cases(), 'value'))->default(LeaveStatus::Awaiting_Replacement_Confirmation->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
    }
};
