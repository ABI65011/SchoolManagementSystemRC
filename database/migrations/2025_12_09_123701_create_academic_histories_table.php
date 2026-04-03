<?php

use App\Helpers\AcademicLevel;
use App\Models\Student;
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
        Schema::create('academic_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Student::class)->constrained()->onDelete('cascade');
            $table->enum('academic_level', array_column(AcademicLevel::cases(), 'value'));
            $table->string('other_academic_level')->nullable();
            $table->string('school_name');
            $table->year('from_year');
            $table->year('to_year');
            $table->string('aggregate_score');
            $table->string('average_position')->nullable();
            $table->string('grade')->nullable();
            $table->string('ple_file')->nullable();
            $table->string('o_level_file')->nullable();
            $table->string('other_file')->nullable();
            $table->boolean('repeat_class')->default(false);
            $table->string('repeated_class')->nullable();
            $table->boolean('skip_class')->default(false);
            $table->boolean('skipped_class')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_histories');
    }
};
