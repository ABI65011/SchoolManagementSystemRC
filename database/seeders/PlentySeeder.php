<?php

namespace Database\Seeders;

use App\Helpers\AssessmentTypeName;
use App\Helpers\AssessmentTypeCategory;
use App\Helpers\ExamType;
use App\Helpers\Term;
use App\Helpers\ExamStatus;
use App\Helpers\AoICriteria;
use App\Helpers\AoICriteriaCode;
use App\Models\AssessmentType;
use App\Models\ContinuousAssessment;
use App\Models\ExamCategory;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\CriterionDefinition;
use App\Models\AoiCriterion;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Classes;
use App\Models\User;
use App\Models\staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PlentySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0');


        AssessmentType::truncate();
        ContinuousAssessment::truncate();
        ExamCategory::truncate();
        Exam::truncate();
        ExamResult::truncate();
        CriterionDefinition::truncate();
        AoiCriterion::truncate();


        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Starting database seeding...');

        // 1. SEED ASSESSMENT TYPES
        $this->command->info('Seeding assessment types...');

        $assessmentTypes = [
            [
                'name' => AssessmentTypeName::Coursework->value,
                'category' => AssessmentTypeCategory::Continuous->value,
                'default_weight' => 25.00,
            ],
            [
                'name' => AssessmentTypeName::Project->value,
                'category' => AssessmentTypeCategory::Continuous->value,
                'default_weight' => 30.00,
            ],
            [
                'name' => AssessmentTypeName::Practical->value,
                'category' => AssessmentTypeCategory::Continuous->value,
                'default_weight' => 20.00,
            ],
            [
                'name' => AssessmentTypeName::Test->value,
                'category' => AssessmentTypeCategory::Continuous->value,
                'default_weight' => 15.00,
            ],

        ];

        foreach ($assessmentTypes as $type) {
            AssessmentType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }


        // 2. SEED CRITERION DEFINITIONS (AoI)

        $this->command->info('Seeding criterion definitions...');

        $criteria = [
            [
                'name' => AoICriteria::Relevance->value,
                'code' => AoICriteriaCode::Relevance->value,
                'description' => 'How relevant is the response to the task?',
                'max_score' => 3,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => AoICriteria::Accuracy->value,
                'code' => AoICriteriaCode::Accuracy->value,
                'description' => 'How accurate and correct is the content?',
                'max_score' => 3,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => AoICriteria::Coherence->value,
                'code' => AoICriteriaCode::Coherence->value,
                'description' => 'How well-organized and logical is the presentation?',
                'max_score' => 3,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => AoICriteria::Excellence->value,
                'code' => AoICriteriaCode::Excellence->value,
                'description' => 'Does it demonstrate exceptional quality?',
                'max_score' => 3,
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($criteria as $criterion) {
            CriterionDefinition::firstOrCreate(
                ['code' => $criterion['code']],
                $criterion
            );
        }


        // 3. SEED EXAM CATEGORIES

        $this->command->info('Seeding exam categories...');


        $internalMain = ExamCategory::firstOrCreate(
            ['name' => 'Internal Exams'],
            [
                'main_category_id' => null,
                'exam_type' => ExamType::Internal->value,
                'description' => 'School-based internal examinations',
                'grading_scale_id' => null,
                'requires_continuous_assessment' => true,
                'weight' => 1.00,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $externalMain = ExamCategory::firstOrCreate(
            ['name' => 'External Exams'],
            [
                'main_category_id' => null,
                'exam_type' => ExamType::External->value,
                'description' => 'External examinations (UNEB, Mock)',
                'grading_scale_id' => null,
                'requires_continuous_assessment' => false,
                'weight' => 1.00,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $subCategories = [
            [
                'name' => 'End of Term',
                'main_category_id' => $internalMain->id,
                'exam_type' => ExamType::Internal->value,
                'description' => 'End of term examinations',
                'grading_scale_id' => null,
                'requires_continuous_assessment' => true,
                'weight' => 1.00,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Mid-Term',
                'main_category_id' => $internalMain->id,
                'exam_type' => ExamType::Internal->value,
                'description' => 'Mid-term assessments',
                'grading_scale_id' => null,
                'requires_continuous_assessment' => true,
                'weight' => 0.50,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Quiz',
                'main_category_id' => $internalMain->id,
                'exam_type' => ExamType::Internal->value,
                'description' => 'Short quizzes and tests',
                'grading_scale_id' => null,
                'requires_continuous_assessment' => true,
                'weight' => 0.25,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Mock',
                'main_category_id' => $externalMain->id,
                'exam_type' => ExamType::External->value,
                'description' => 'Pre-UNEB mock examinations',
                'grading_scale_id' => null,
                'requires_continuous_assessment' => false,
                'weight' => 1.00,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'UNEB Final',
                'main_category_id' => $externalMain->id,
                'exam_type' => ExamType::External->value,
                'description' => 'Official UNEB examinations',
                'grading_scale_id' => null,
                'requires_continuous_assessment' => true,
                'weight' => 1.00,
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($subCategories as $subCategory) {
            ExamCategory::firstOrCreate(
                [
                    'name' => $subCategory['name'],
                    'main_category_id' => $subCategory['main_category_id']
                ],
                $subCategory
            );
        }

        // 4. CREATE TEST STUDENTS AND CLASSES IF NEEDED

        $this->command->info('Checking for classes and students...');


        $staff = staff::first();


        if (!$staff) {
            $user = User::firstOrCreate(
                ['email' => 'class_teacher@school.com'],
                [
                    'name' => 'Default Class Teacher',
                    'password' => Hash::make('password'),
                ]
            );

            $staff = staff::create([
                'user_id' => $user->id,
            ]);
        }


        if (Classes::count() === 0) {
            $classes = ['S.1', 'S.2', 'S.3', 'S.4', 'S.5', 'S.6'];
            foreach ($classes as $className) {
                Classes::create([
                    'name' => $className,
                    'code' => str_replace('.', '', $className),
                    'academic_year' => date('Y'),
                    'class_teacher_id' => $staff->id,
                    'is_active' => true,
                    'description' => $className . ' Class',
                ]);
            }
        }


        if (User::where('email', 'teacher@school.com')->doesntExist()) {
            $user = User::create([
                'name' => 'Test Teacher',
                'email' => 'teacher@school.com',
                'password' => Hash::make('password'),
            ]);

            staff::create([
                'user_id' => $user->id,
            ]);
        }


        // 5. SEED EXAMS

        $this->command->info('Seeding exams...');

        $classes = Classes::all();
        $categories = ExamCategory::whereNotNull('main_category_id')->get();
        $currentYear = date('Y');

        if ($classes->isNotEmpty() && $categories->isNotEmpty()) {
            $examData = [];


            foreach ($classes as $class) {
                foreach ($categories as $category) {

                    if (rand(0, 1) && count($examData) < 10) {
                        $term = (string) rand(1, 3);
                        $statuses = [ExamStatus::Draft->value, ExamStatus::Published->value, ExamStatus::Completed->value];

                        $examData[] = [
                            'name' => $category->name . ' Examination ' . $currentYear,
                            'code' => strtoupper(substr($category->name, 0, 3)) . '-' . $currentYear . '-T' . $term,
                            'exam_category_id' => $category->id,
                            'class_id' => $class->id,
                            'term' => $term,
                            'year' => $currentYear,
                            'exam_date' => now()->addDays(rand(1, 30))->format('Y-m-d'),
                            'entry_start_date' => now()->subDays(rand(1, 10))->format('Y-m-d'),
                            'entry_end_date' => now()->addDays(rand(5, 15))->format('Y-m-d'),
                            'result_release_date' => now()->addDays(rand(20, 40))->format('Y-m-d'),
                            'max_mark' => 100,
                            'weight' => 1.00,
                            'requires_continuous_assessment' => $category->requires_continuous_assessment,
                            'status' => $statuses[array_rand($statuses)],
                            'description' => 'Sample exam description for ' . $category->name,
                            'instructions' => 'Read all questions carefully. Answer all questions.',
                            'is_active' => true,
                        ];
                    }
                }
            }

            foreach ($examData as $exam) {
                Exam::firstOrCreate(
                    [
                        'name' => $exam['name'],
                        'class_id' => $exam['class_id'],
                        'term' => $exam['term'],
                        'year' => $exam['year']
                    ],
                    $exam
                );
            }
        }

        // 6. SEED CONTINUOUS ASSESSMENTS

        $this->command->info('Seeding continuous assessments...');

        $students = Student::with('class')->get();
        $subjects = Subject::all();
        $assessmentTypeIds = AssessmentType::where('category', AssessmentTypeCategory::Continuous->value)->pluck('id')->toArray();
        $staff = staff::first();

        if ($students->isNotEmpty() && $subjects->isNotEmpty() && !empty($assessmentTypeIds) && $staff) {
            $caCount = 0;
            foreach ($students as $student) {

                for ($i = 0; $i < rand(2, 3); $i++) {
                    if ($caCount >= 30) break;

                    $subject = $subjects->random();
                    $rawScore = rand(40, 95);
                    $maxScore = 100;
                    $term = (string) rand(1, 3);

                    ContinuousAssessment::create([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'assessment_type_id' => $assessmentTypeIds[array_rand($assessmentTypeIds)],
                        'class_id' => $student->class_id,
                        'term' => $term,
                        'year' => $currentYear,
                        'title' => 'Assessment ' . ($i + 1) . ' - ' . $subject->name,
                        'raw_score' => $rawScore,
                        'max_score' => $maxScore,
                        'teacher_comment' => 'Good progress shown',
                        'recorded_by' => $staff->user_id,
                    ]);

                    $caCount++;
                }
                if ($caCount >= 30) break;
            }
        }


        // 7. SEED EXAM RESULTS

        $this->command->info('Seeding exam results...');

        $exams = Exam::where('status', ExamStatus::Completed->value)->get();
        $students = Student::all();
        $subjects = Subject::all();
        $staff = staff::first();

        if ($exams->isNotEmpty() && $students->isNotEmpty() && $subjects->isNotEmpty() && $staff) {
            $resultCount = 0;
            foreach ($exams as $exam) {

                $classStudents = $students->where('class_id', $exam->class_id);

                foreach ($classStudents as $student) {
                    if ($resultCount >= 50) break;

                    foreach ($subjects->random(rand(3, 5)) as $subject) {
                        $rawMark = rand(40, 95);

                        ExamResult::create([
                            'exam_id' => $exam->id,
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'raw_mark' => $rawMark,
                            'continuous_assessment_contribution' => rand(10, 20),
                            'final_mark' => $rawMark,
                            'grade' => $this->calculateGrade($rawMark),
                            'teacher_remark' => 'Good performance',
                            'graded_by' => $staff->user_id,
                        ]);

                        $resultCount++;
                    }
                }
                if ($resultCount >= 50) break;
            }
        }


        // 8. SEED AOI CRITERIA

        $this->command->info('Seeding AoI criteria...');

        $examResults = ExamResult::inRandomOrder()->limit(30)->get();
        $criteriaDefinitions = CriterionDefinition::all();
        $staff = staff::first();

        foreach ($examResults as $examResult) {
            foreach ($criteriaDefinitions as $criterion) {
                
                if (rand(0, 1)) {
                    AoiCriterion::create([
                        'exam_result_id' => $examResult->id,
                        'criterion_definition_id' => $criterion->id,
                        'score' => rand(1, 3),
                        'graded_by' => $staff?->user_id ?? $examResult->graded_by,
                        'comment' => rand(0, 1) ? 'Good work on this criterion' : null,
                    ]);
                }
            }
        }

        $this->command->info('Database seeding completed successfully!');
    }

    /**
     * Helper function to calculate grade based on mark
     */
    private function calculateGrade($mark)
    {
        if ($mark >= 80) return 'A';
        if ($mark >= 70) return 'B';
        if ($mark >= 60) return 'C';
        if ($mark >= 50) return 'D';
        return 'E';
    }
}
