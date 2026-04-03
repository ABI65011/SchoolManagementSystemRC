<?php

namespace Database\Seeders;

use App\Helpers\AoICriteria;
use App\Helpers\AoICriteriaCode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CriterionDefinitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $criteria = [
            [
                'name' => AoICriteria::Relevance->value,
                'code' => AoICriteriaCode::Relevance->value,
                'description' => 'How relevant is the response to the task?',
                'sort_order' => 1,
            ],
            [
                'name' => AoICriteria::Accuracy->value,
                'code' => AoICriteriaCode::Accuracy->value,
                'description' => 'How accurate and correct is the content?',
                'sort_order' => 2,
            ],
            [
                'name' => AoICriteria::Coherence->value,
                'code' => AoICriteriaCode::Coherence->value,
                'description' => 'How well-organized and logical is the presentation?',
                'sort_order' => 3,
            ],
            [
                'name' => AoICriteria::Excellence->value,
                'code' => AoICriteriaCode::Excellence->value,
                'description' => 'Does it demonstrate exceptional quality?',
                'sort_order' => 4,
            ],
        ];

        foreach ($criteria as $criterion) {
            DB::table('criterion_definitions')->updateOrInsert(
                ['code' => $criterion['code']],
                array_merge($criterion, [
                    'max_score' => 3,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
