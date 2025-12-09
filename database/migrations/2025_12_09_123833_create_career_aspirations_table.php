<?php

use App\Helpers\CareerAspirations;
use App\Helpers\Subjects;
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
        Schema::create('career_aspirations', function (Blueprint $table) {
            $table->id();
            $table->enum('aspiration', array_column(CareerAspirations::cases(), 'value'))->nullable();
            $table->string('other_aspiration')->nullable();
            $table->enum('best_done_subjects', array_column(Subjects::cases(), 'value'))->nullable();
            $table->string('other_best_done_subjects')->nullable();
            $table->enum('worst_done_subjects', array_column(Subjects::cases(), 'value'))->nullable();
            $table->string('other_worst_done_subjects')->nullable();
            $table->enum('favorite_subjects', array_column(Subjects::cases(), 'value'))->nullable();
            $table->string('other_favorite_subjects')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_aspirations');
    }
};
