<?php

namespace Database\Seeders;

use App\Helpers\GradingScaleName;
use App\Helpers\GradingScaleType;
use App\Models\GradingScale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradingScaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $uceScale = GradingScale::create([
            'name' => GradingScaleName::UNEB_Traditional,
            'type' => GradingScaleType::UNEB_Traditional,
            'is_default' => true,
        ]);

        $uceGrades = [
            ['grade_code' => 'D1', 'min_mark' => 85, 'max_mark' => 100, 'points' => 1, 'order' => 1],
            ['grade_code' => 'D2', 'min_mark' => 80, 'max_mark' => 84, 'points' => 2, 'order' => 2],
            ['grade_code' => 'C3', 'min_mark' => 75, 'max_mark' => 79, 'points' => 3, 'order' => 3],
            ['grade_code' => 'C4', 'min_mark' => 70, 'max_mark' => 74, 'points' => 4, 'order' => 4],
            ['grade_code' => 'C5', 'min_mark' => 65, 'max_mark' => 69, 'points' => 5, 'order' => 5],
            ['grade_code' => 'C6', 'min_mark' => 60, 'max_mark' => 64, 'points' => 6, 'order' => 6],
            ['grade_code' => 'P7', 'min_mark' => 55, 'max_mark' => 59, 'points' => 7, 'order' => 7],
            ['grade_code' => 'P8', 'min_mark' => 50, 'max_mark' => 54, 'points' => 8, 'order' => 8],
            ['grade_code' => 'F9', 'min_mark' => 0, 'max_mark' => 49, 'points' => 9, 'order' => 9],
        ];

        foreach ($uceGrades as $grade) {
            $uceScale->items()->create($grade);
        }


        $uaceScale = GradingScale::create([
            'name' => 'UACE (A-Level)',
            'type' => 'uace',
            'is_default' => false,
        ]);

        $uaceGrades = [
            ['grade_code' => 'A', 'min_mark' => 80, 'max_mark' => 100, 'points' => 6, 'order' => 1],
            ['grade_code' => 'B', 'min_mark' => 70, 'max_mark' => 79, 'points' => 5, 'order' => 2],
            ['grade_code' => 'C', 'min_mark' => 60, 'max_mark' => 69, 'points' => 4, 'order' => 3],
            ['grade_code' => 'D', 'min_mark' => 50, 'max_mark' => 59, 'points' => 3, 'order' => 4],
            ['grade_code' => 'E', 'min_mark' => 40, 'max_mark' => 49, 'points' => 2, 'order' => 5],
            ['grade_code' => 'O', 'min_mark' => 35, 'max_mark' => 39, 'points' => 1, 'order' => 6],
            ['grade_code' => 'F', 'min_mark' => 0, 'max_mark' => 34, 'points' => 0, 'order' => 7],
        ];

        foreach ($uaceGrades as $grade) {
            $uaceScale->items()->create($grade);
        }

        
        $competencyScale = GradingScale::create([
            'name' => GradingScaleName::Competency_Based,
            'type' => GradingScaleType::Competency_Based,
            'is_default' => false,
        ]);

        $competencyGrades = [
            ['grade_code' => 'A', 'min_mark' => 80, 'max_mark' => 100, 'achievement_level' => 'Exceptional', 'order' => 1],
            ['grade_code' => 'B', 'min_mark' => 70, 'max_mark' => 79, 'achievement_level' => 'Outstanding', 'order' => 2],
            ['grade_code' => 'C', 'min_mark' => 60, 'max_mark' => 69, 'achievement_level' => 'Satisfactory', 'order' => 3],
            ['grade_code' => 'D', 'min_mark' => 50, 'max_mark' => 59, 'achievement_level' => 'Basic', 'order' => 4],
            ['grade_code' => 'E', 'min_mark' => 35, 'max_mark' => 49, 'achievement_level' => 'Elementary', 'order' => 5],
            ['grade_code' => 'U', 'min_mark' => 0, 'max_mark' => 34, 'achievement_level' => 'Ungraded', 'order' => 6],
        ];

        foreach ($competencyGrades as $grade) {
            $competencyScale->items()->create($grade);
        }
    }
}
