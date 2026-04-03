<?php

use App\Helpers\ExamType;
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
        Schema::create('exam_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('main_category_id')->nullable()->constrained('exam_categories')->onDelete('cascade')->comment('The broader category this belongs to. NULL means this IS a main category.');
            $table->enum('exam_type', array_column(ExamType::cases(), 'value'));
            $table->string('description');
            $table->foreignId('grading_scale_id')->nullable()->constrained();
            $table->boolean('requires_continuous_assessment')->default(false);
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['name', 'main_category_id'], 'unique_subcategory_per_main');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_categories');
    }
};
