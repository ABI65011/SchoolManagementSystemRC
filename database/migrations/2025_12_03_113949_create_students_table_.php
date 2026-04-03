<?php

use App\Helpers\ApplyingSection;
use App\Helpers\Classes;
use App\Helpers\Gender;
use App\Helpers\IDType;
use App\Helpers\ReligiousAffiliation;
use App\Models\AcademicHistory;
use App\Models\CareerAspiration;
use App\Models\DisciplineHistory;
use App\Models\MedicalHistory;
use App\Models\User;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->onDelete('cascade');
            $table->string('identification_image');
            $table->year('admission_year');
            $table->enum('joining_class', array_column(Classes::cases(), 'value'));
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('dob');
            $table->enum('gender', array_column(Gender::cases(), 'value'));
            $table->json('citizenship');
            $table->enum('id_type', array_column(IDType::cases(), 'value'));
            $table->string('id_no')->unique()->nullable();
            $table->string('id_image_path');
            $table->string('a_level_combination')->nullable();
            $table->enum('applying_section', array_column(ApplyingSection::cases(), 'value'));
            $table->enum('religious_affiliation', array_column(ReligiousAffiliation::cases(), 'value'));
            $table->string('other_religious_affiliation')->nullable();
            $table->boolean('has_additional_info')->default(false);
            $table->string('additional_info')->nullable();
            $table->json('spoken_languages');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
