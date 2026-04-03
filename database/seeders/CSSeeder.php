<?php

namespace Database\Seeders;

use App\Helpers\AssessmentTypeCategory;
use App\Helpers\SubjectCategory;
use App\Helpers\SubjectCode;
use App\Helpers\Subjects;
use App\Models\AssessmentType;
use App\Models\Classes;
use App\Models\ContinuousAssessment;
use App\Models\staff;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Stream::truncate();

        $levels = Classes::all();

        if ($levels->isEmpty()) {
            $this->command->warn('No Levels found. Please seed Levels before running StreamSeeder.');
            return;
        }

        $streamNames = ['A', 'B', 'C', 'D'];

        foreach ($levels as $level) {
            foreach ($streamNames as $index => $name) {
                Stream::create([
                    'class_id' => $level->id,
                    'name'     => $name,
                    'code'     => strtoupper($level->name . '-' . $name), // e.g., S1-A
                    'order'    => $index + 1,
                ]);
            }
        }

        $this->command->info('Streams seeded successfully!');
    }
}
