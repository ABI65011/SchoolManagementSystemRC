<?php

use App\Helpers\GradingScaleName;
use App\Helpers\GradingScaleType;
use App\Helpers\UceGrade;
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
        Schema::create('grading_scales', function (Blueprint $table) {
            $table->id();
            $table->enum('name', array_column(GradingScaleName::cases(), 'value'));
            $table->enum('type', array_column(GradingScaleType::cases(), 'value'));
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('grading_scale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_scale_id')->constrained()->cascadeOnDelete();
            $table->string('grade_code');
            $table->integer('min_mark');
            $table->integer('max_mark');
            $table->string('achievement_level')->nullable();
            $table->text('descriptor')->nullable();
            $table->integer('points')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_scale_items');
        Schema::dropIfExists('grading_scales');
    }
};
