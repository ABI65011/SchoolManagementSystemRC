<?php

use App\Helpers\AoICriteria;
use App\Helpers\AoICriteriaCode;
use App\Helpers\ExamStatus;
use App\Helpers\ExamType;
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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->foreignId('exam_category_id')->nullable()->constrained('exam_categories')->nullOnDelete();

            $table->foreignId('class_id')->constrained();
            $table->enum('term', array_column(Term::cases(), 'value'));
            $table->year('year');
            $table->date('exam_date')->nullable();
            $table->date('entry_start_date')->nullable();
            $table->date('entry_end_date')->nullable();
            $table->date('result_release_date')->nullable();
            $table->decimal('max_mark', 5, 2)->default(100);
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->boolean('requires_continuous_assessment')->default(true);
            $table->enum('status', array_column(ExamStatus::cases(), 'value'))->default(ExamStatus::Draft->value);
            $table->string('description')->nullable();
            $table->string('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('exam_date');
            $table->index('status');
        });

        Schema::create(
            'exam_results',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained();
                $table->foreignId('student_id')->constrained();
                $table->foreignId('subject_id')->constrained();
                $table->decimal('raw_mark', 5, 2)->nullable();
                $table->decimal('final_mark', 5, 2)->nullable();
                $table->string('grade')->nullable();
                $table->text('teacher_remark')->nullable();
                $table->foreignId('graded_by')->constrained('users');
                $table->timestamps();

                $table->unique(['exam_id', 'student_id', 'subject_id']);

                $table->index(['student_id', 'exam_id']);
                $table->index('grade');
            }
        );

        Schema::create('criterion_definitions', function (Blueprint $table) {
            $table->id();
            $table->enum('name', array_column(AoICriteria::cases(), 'value'))->unique();
            $table->enum('code', array_column(AoICriteriaCode::cases(), 'value'))->unique();
            $table->text('description')->nullable();
            $table->integer('max_score')->default(3);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });


        Schema::create(
            'aoi_criteria',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_result_id')->constrained()->cascadeOnDelete();
                $table->foreignId('criterion_definition_id')->nullable()->constrained();
                $table->tinyInteger('score')->unsigned();
                $table->foreignId('graded_by')->nullable()->constrained('users');
                $table->text('comment')->nullable();
                $table->timestamps();

                $table->unique(['exam_result_id', 'criterion_definition_id'], 'unique_criterion_per_result');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aoi_criteria');
        Schema::dropIfExists('criterion_definitions');
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('exams');
    }
};
