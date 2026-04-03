<?php

namespace App\Observers;

use App\Helpers\AdmissionStatus;
use App\Models\Admission;
use App\Models\Classes;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class StudentObserver
{
    /**
     * Handle the Student "created" event.
     */
    public function created(Student $student): void
    {
        Admission::create([
            'students_id' => $student->id,
            'status' => AdmissionStatus::Pending->value,
        ]);

        $this->assignClass($student);
    }

    /**
     * Handle the Student "updated" event.
     */
    public function updated(Student $student): void
    {
        if ($student->isDirty('joining_class')) {
            $this->assignClass($student);
        }
    }

    /**
     * Handle the Student "deleted" event.
     */
    public function deleted(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "restored" event.
     */
    public function restored(Student $student): void
    {
        //
    }

    /**
     * Handle the Student "force deleted" event.
     */
    public function forceDeleted(Student $student): void
    {
        //
    }
    private function assignToClass(Student $student): void
    {
        if (empty($student->joining_class)) {
            return;
        }
        $class = Classes::where('name', $student->joining_class)
            ->orWhere('code', $student->joining_class)
            ->first();

        if ($class) {

            $exists = $student->classes()
                ->where('class_id', $class->id)
                ->exists();

            if (!$exists) {
                $student->classes()->attach($class->id);

                Log::info('Student assigned to class', [
                    'student_id' => $student->id,
                    'class_id' => $class->id
                ]);
            }
        }
    }

    /**
     * Find class ID based on joining class name/code
     */
    private function findClassId(string $joiningClass): ?int
    {
        $class = Classes::where('name', $joiningClass)
            ->orWhere('code', $joiningClass)
            ->orWhere('name', 'LIKE', '%' . $joiningClass . '%')
            ->first();

        if ($class) {
            return $class->id;
        }


        $standardized = $this->standardizeClassName($joiningClass);

        if ($standardized) {
            $class = Classes::where('name', $standardized)
                ->orWhere('code', $standardized)
                ->first();

            if ($class) {
                return $class->id;
            }
        }

        return null;
    }

    /**
     * Standardize common class name variations
     */
    private function standardizeClassName(string $className): ?string
    {
        $className = strtoupper(trim($className));


        $clean = preg_replace('/[.\s]/', '', $className);

        
        $map = [
            'S1' => 'Senior 1',
            'S.1' => 'Senior 1',
            'SENIOR1' => 'Senior 1',
            'FORM1' => 'Senior 1',

            'S2' => 'Senior 2',
            'S.2' => 'Senior 2',
            'SENIOR2' => 'Senior 2',
            'FORM2' => 'Senior 2',

            'S3' => 'Senior 3',
            'S.3' => 'Senior 3',
            'SENIOR3' => 'Senior 3',
            'FORM3' => 'Senior 3',

            'S4' => 'Senior 4',
            'S.4' => 'Senior 4',
            'SENIOR4' => 'Senior 4',
            'FORM4' => 'Senior 4',

            'S5' => 'Senior 5',
            'S.5' => 'Senior 5',
            'SENIOR5' => 'Senior 5',
            'FORM5' => 'Senior 5',
            'S5-PCB' => 'Senior 5 - PCB',
            'S5-PCM' => 'Senior 5 - PCM',
            'S5-MEG' => 'Senior 5 - MEG',

            'S6' => 'Senior 6',
            'S.6' => 'Senior 6',
            'SENIOR6' => 'Senior 6',
            'FORM6' => 'Senior 6',
            'S6-PCB' => 'Senior 6 - PCB',
            'S6-PCM' => 'Senior 6 - PCM',
            'S6-MEG' => 'Senior 6 - MEG',
        ];

        return $map[$clean] ?? $map[$className] ?? null;
    }

    public function assignClass(Student $student): void
    {
        if (empty($student->joining_class)) {
            Log::warning('Student has no joining_class specified', ['student_id' => $student->id]);
            return;
        }

        $classId = $this->findClassId($student->joining_class);

        if ($classId) {
            $student->class_id = $classId;

            Log::info('Class assigned to student', [
                'student_id' => $student->id,
                'joining_class' => $student->joining_class,
                'class_id' => $classId
            ]);
        } else {
            Log::error('No class found for joining_class', [
                'student_id' => $student->id,
                'joining_class' => $student->joining_class
            ]);
        }
    }
}
