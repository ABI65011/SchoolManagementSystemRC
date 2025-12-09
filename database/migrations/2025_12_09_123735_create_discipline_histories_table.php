<?php

use App\Helpers\DisciplineAction;
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
        Schema::create('discipline_histories', function (Blueprint $table) {
            $table->id();
            $table->boolean('has_disciplinary_issues')->default(false);
            $table->enum('disciplinary_issues', array_column(DisciplineAction::cases(), 'value'))->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipline_histories');
    }
};
