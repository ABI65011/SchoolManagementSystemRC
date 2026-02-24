<?php

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
        Schema::create('attendance_locations', function (Blueprint $table) {
            $table->id();
            $table->string('location_name');
            $table->decimal('school_latitude', 10, 8);
            $table->decimal('school_longitude', 11, 8);
            $table->integer('geofence_radius')->default(200); // meters
            $table->time('late_threshold')->default('08:00:00');
            $table->decimal('full_day_hours', 4, 2)->default(9.0);
            $table->time('working_day_start')->default('08:00:00');
            $table->time('working_day_end')->default('17:00:00');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_locations');
    }
};
