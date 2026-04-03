<?php

use App\Helpers\CareerAspirations;
use App\Helpers\Subjects;
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
        Schema::create('career_aspirations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Student::class)->constrained()->onDelete('cascade');
            $table->enum('aspiration', array_column(CareerAspirations::cases(), 'value'))->nullable();
            $table->json('best_done_subjects')->nullable();
            $table->json('worst_done_subjects')->nullable();
            $table->json('favorite_subjects')->nullable();
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
