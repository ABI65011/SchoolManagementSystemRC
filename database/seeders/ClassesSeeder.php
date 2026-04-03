<?php

namespace Database\Seeders;

use App\Helpers\Classes as HelpersClasses;
use App\Models\Classes;
use App\Models\staff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffMembers = staff::with('user')->get();


        if ($staffMembers->isEmpty()) {
            $this->command->warn('No staff members found. Please run StaffSeeder first or create a placeholder staff.');
            $this->command->info('Creating a placeholder staff member for class teachers...');


            $this->command->error('Cannot proceed without staff members. Please seed staff first.');
            return;
        }

        $classes = [

            [
                'name' => HelpersClasses::S1->value,
                'code' => 'S1',
                'academic_year' => date('Y'),
                'class_teacher_id' => $this->getRandomStaffId($staffMembers),
                'is_active' => true,
                'description' => 'Ordinary Level - First Year',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => HelpersClasses::S2->value,
                'code' => 'S2',
                'academic_year' => date('Y'),
                'class_teacher_id' => $this->getRandomStaffId($staffMembers),
                'is_active' => true,
                'description' => 'Ordinary Level - Second Year',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => HelpersClasses::S3->value,
                'code' => 'S3',
                'academic_year' => date('Y'),
                'class_teacher_id' => $this->getRandomStaffId($staffMembers),
                'is_active' => true,
                'description' => 'Ordinary Level - Third Year',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => HelpersClasses::S4->value,
                'code' => 'S4',
                'academic_year' => date('Y'),
                'class_teacher_id' => $this->getRandomStaffId($staffMembers),
                'is_active' => true,
                'description' => 'Ordinary Level - Fourth Year (UCE Candidate Class)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => HelpersClasses::S5->value,
                'code' => 'S5',
                'academic_year' => date('Y'),
                'class_teacher_id' => $this->getRandomStaffId($staffMembers),
                'is_active' => true,
                'description' => 'Advanced Level - First Year',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => HelpersClasses::S6->value,
                'code' => 'S6',
                'academic_year' => date('Y'),
                'class_teacher_id' => $this->getRandomStaffId($staffMembers),
                'is_active' => true,
                'description' => 'Advanced Level - Second Year (UACE Candidate Class)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        foreach ($classes as $classData) {
            Classes::firstOrCreate(
                ['name' => $classData['name']],
                [
                    'name' => $classData['name'],
                    'code' => $classData['code'],
                    'academic_year' => $classData['academic_year'],
                    'class_teacher_id' => $classData['class_teacher_id'],
                    'is_active' => $classData['is_active'],
                    'description' => $classData['description'],
                ]
            );
        }

        $this->command->info('Classes seeded successfully with class teachers!');
    }


    private function getRandomStaffId($staffMembers)
    {
        if ($staffMembers->isEmpty()) {
            return null;
        }

        return $staffMembers->random()->id;
    }

}
