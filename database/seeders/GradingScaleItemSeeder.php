<?php

namespace Database\Seeders;

use App\Models\GradingScale;
use App\Models\GradingScaleItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradingScaleItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $scales = GradingScale::all();

        foreach ($scales as $scale) {

            if ($scale->items()->count() > 0) {
                $this->command->info("Scale {$scale->name->value} already has items. Skipping...");
                continue;
            }

            $this->command->info("Seeding items for {$scale->name->value}...");

            
            switch ($scale->type->value) {
                case 'uneb_traditional':
                    $this->seedUNEBTraditional($scale);
                    break;
                case 'uace':
                    $this->seedUACE($scale);
                    break;
                case 'competency_based':
                    $this->seedCompetencyBased($scale);
                    break;
                default:
                    $this->command->warn("Unknown scale type: {$scale->type->value}");
            }
        }
    }

    /**
     * Seed UNEB Traditional scale (UCE)
     */
    private function seedUNEBTraditional(GradingScale $scale): void
    {
        $grades = [
            [
                'grade_code' => 'D1',
                'min_mark' => 85,
                'max_mark' => 100,
                'achievement_level' => 'Excellent',
                'descriptor' => 'Distinction One',
                'points' => 1,
                'order' => 1,
            ],
            [
                'grade_code' => 'D2',
                'min_mark' => 80,
                'max_mark' => 84,
                'achievement_level' => 'Very Good',
                'descriptor' => 'Distinction Two',
                'points' => 2,
                'order' => 2,
            ],
            [
                'grade_code' => 'C3',
                'min_mark' => 75,
                'max_mark' => 79,
                'achievement_level' => 'Good',
                'descriptor' => 'Credit Three',
                'points' => 3,
                'order' => 3,
            ],
            [
                'grade_code' => 'C4',
                'min_mark' => 70,
                'max_mark' => 74,
                'achievement_level' => 'Fairly Good',
                'descriptor' => 'Credit Four',
                'points' => 4,
                'order' => 4,
            ],
            [
                'grade_code' => 'C5',
                'min_mark' => 65,
                'max_mark' => 69,
                'achievement_level' => 'Above Average',
                'descriptor' => 'Credit Five',
                'points' => 5,
                'order' => 5,
            ],
            [
                'grade_code' => 'C6',
                'min_mark' => 60,
                'max_mark' => 64,
                'achievement_level' => 'Average',
                'descriptor' => 'Credit Six',
                'points' => 6,
                'order' => 6,
            ],
            [
                'grade_code' => 'P7',
                'min_mark' => 55,
                'max_mark' => 59,
                'achievement_level' => 'Below Average',
                'descriptor' => 'Pass Seven',
                'points' => 7,
                'order' => 7,
            ],
            [
                'grade_code' => 'P8',
                'min_mark' => 50,
                'max_mark' => 54,
                'achievement_level' => 'Pass',
                'descriptor' => 'Pass Eight',
                'points' => 8,
                'order' => 8,
            ],
            [
                'grade_code' => 'F9',
                'min_mark' => 0,
                'max_mark' => 49,
                'achievement_level' => 'Fail',
                'descriptor' => 'Fail Nine',
                'points' => 9,
                'order' => 9,
            ],
        ];

        foreach ($grades as $grade) {
            $scale->items()->create($grade);
        }

        $this->command->info('Added ' . count($grades) . ' items to UNEB Traditional scale');
    }

    /**
     * Seed UACE scale (A-Level with points)
     */
    private function seedUACE(GradingScale $scale): void
    {
        $grades = [
            [
                'grade_code' => 'A',
                'min_mark' => 80,
                'max_mark' => 100,
                'achievement_level' => 'Excellent',
                'descriptor' => 'A - 6 points',
                'points' => 6,
                'order' => 1,
            ],
            [
                'grade_code' => 'B',
                'min_mark' => 70,
                'max_mark' => 79,
                'achievement_level' => 'Very Good',
                'descriptor' => 'B - 5 points',
                'points' => 5,
                'order' => 2,
            ],
            [
                'grade_code' => 'C',
                'min_mark' => 60,
                'max_mark' => 69,
                'achievement_level' => 'Good',
                'descriptor' => 'C - 4 points',
                'points' => 4,
                'order' => 3,
            ],
            [
                'grade_code' => 'D',
                'min_mark' => 50,
                'max_mark' => 59,
                'achievement_level' => 'Satisfactory',
                'descriptor' => 'D - 3 points',
                'points' => 3,
                'order' => 4,
            ],
            [
                'grade_code' => 'E',
                'min_mark' => 40,
                'max_mark' => 49,
                'achievement_level' => 'Pass',
                'descriptor' => 'E - 2 points',
                'points' => 2,
                'order' => 5,
            ],
            [
                'grade_code' => 'O',
                'min_mark' => 35,
                'max_mark' => 39,
                'achievement_level' => 'Subsidiary',
                'descriptor' => 'O - 1 point',
                'points' => 1,
                'order' => 6,
            ],
            [
                'grade_code' => 'F',
                'min_mark' => 0,
                'max_mark' => 34,
                'achievement_level' => 'Fail',
                'descriptor' => 'F - 0 points',
                'points' => 0,
                'order' => 7,
            ],
        ];

        foreach ($grades as $grade) {
            $scale->items()->create($grade);
        }

        $this->command->info('Added ' . count($grades) . ' items to UACE scale');
    }

    /**
     * Seed Competency-Based scale (New Curriculum)
     */
    private function seedCompetencyBased(GradingScale $scale): void
    {
        $grades = [
            [
                'grade_code' => 'A',
                'min_mark' => 80,
                'max_mark' => 100,
                'achievement_level' => 'Exceptional',
                'descriptor' => 'Achieved Excellence',
                'points' => null,
                'order' => 1,
            ],
            [
                'grade_code' => 'B',
                'min_mark' => 70,
                'max_mark' => 79,
                'achievement_level' => 'Outstanding',
                'descriptor' => 'Achieved Above Standard',
                'points' => null,
                'order' => 2,
            ],
            [
                'grade_code' => 'C',
                'min_mark' => 60,
                'max_mark' => 69,
                'achievement_level' => 'Satisfactory',
                'descriptor' => 'Achieved Standard',
                'points' => null,
                'order' => 3,
            ],
            [
                'grade_code' => 'D',
                'min_mark' => 50,
                'max_mark' => 59,
                'achievement_level' => 'Basic',
                'descriptor' => 'Achieved Basic Competency',
                'points' => null,
                'order' => 4,
            ],
            [
                'grade_code' => 'E',
                'min_mark' => 35,
                'max_mark' => 49,
                'achievement_level' => 'Elementary',
                'descriptor' => 'Below Standard',
                'points' => null,
                'order' => 5,
            ],
            [
                'grade_code' => 'U',
                'min_mark' => 0,
                'max_mark' => 34,
                'achievement_level' => 'Ungraded',
                'descriptor' => 'Ungraded',
                'points' => null,
                'order' => 6,
            ],
        ];

        foreach ($grades as $grade) {
            $scale->items()->create($grade);
        }

        $this->command->info('Added ' . count($grades) . ' items to Competency-Based scale');
    }
}
