<?php

use App\Helpers\AssessmentType;
use App\Helpers\AssessmentTypeCategory;
use App\Helpers\AssessmentTypeName;
use App\Helpers\Term;
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
        Schema::create('assessment_types', function (Blueprint $table) {
            $table->id();
            $table->enum('name', array_column(AssessmentTypeName::cases(), 'value'))->unique();
            $table->enum('category', array_column(AssessmentTypeCategory::cases(), 'value'));
            $table->decimal('default_weight', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('continuous_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('assessment_type_id')->constrained();
            $table->foreignId('class_id')->constrained();
            $table->enum('term', array_column(Term::cases(), 'value'));
            $table->year('year');
            $table->string('title')->nullable();
            $table->decimal('raw_score', 5, 2);
            $table->decimal('max_score', 5, 2)->default(100);
            $table->decimal('weighted_score', 5, 2)->storedAs('(raw_score / max_score) * 100');
            $table->text('teacher_comment')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();

            $table->index(['student_id', 'subject_id', 'term', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('continuous_assessments');
        Schema::dropIfExists('assessment_types');
    }
};
