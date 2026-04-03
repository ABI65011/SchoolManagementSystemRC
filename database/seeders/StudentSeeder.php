<?php

namespace Database\Seeders;

use App\Helpers\AcademicLevel;
use App\Helpers\CareerAspirations;
use App\Helpers\DisciplineAction;
use App\Helpers\IDType;
use App\Helpers\Subjects;
use App\Helpers\UserRoles;
use App\Models\AcademicHistory;
use App\Models\CareerAspiration;
use App\Models\DisciplineHistory;
use App\Models\MedicalHistory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        CareerAspiration::truncate();
        DisciplineHistory::truncate();
        MedicalHistory::truncate();
        AcademicHistory::truncate();
        Student::truncate();
        User::whereHas('student')->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        Storage::disk('public')->makeDirectory('profile');
        Storage::disk('public')->makeDirectory('identity');

        $firstNames = ['John', 'Jane', 'Michael', 'Sarah', 'David', 'Emily', 'Christopher', 'Amanda', 'Daniel', 'Jessica'];
        $middleNames = ['Paul', 'Marie', 'James', 'Elizabeth', 'Robert', 'Anne', 'William', 'Louise', 'Thomas', 'Grace'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'];
        $schools = ['Kampala High School', 'Makerere College', 'Ntare School', 'St. Mary Kitende', 'Budo Junior', 'Kings College Budo', 'St. Francis School', 'Namagunga Girls', 'SMASK', 'Kibuli Secondary'];
        $languages = [['English', 'Swahili'], ['English', 'Luganda'], ['English', 'Runyankole'], ['English', 'Luo'], ['English', 'Ateso'], ['English', 'Lugbara']];
        $citizenships = [['Ugandan'], ['Ugandan', 'Kenyan'], ['Ugandan', 'Rwandan'], ['Ugandan', 'Tanzanian'], ['Ugandan', 'South Sudanese']];

        for ($i = 0; $i < 20; $i++) {
            DB::transaction(function () use ($i, $firstNames, $middleNames, $lastNames, $schools, $languages, $citizenships) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $email = strtolower($firstName . '.' . $lastName . ($i + 1) . '@example.com');

                $user = User::create([
                    'name' => $firstName . ' ' . $lastName,
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'remember_token' => Str::random(10),
                ]);

                $user->syncRoles(UserRoles::Student->value);

                $dob = now()->subYears(rand(15, 20))->subDays(rand(0, 365));
                $admissionYear = rand(2020, 2024);
                $joiningClass = 'S.'.rand(1, 6);
                $idTypeCases = IDType::cases();
                $idType = $idTypeCases[array_rand($idTypeCases)];
                $idTypeValue = $idType->value;


                $profileImagePath = 'profile/' . Str::uuid() . '.jpg';
                $idImagePath = 'identity/' . Str::uuid() . '.jpg';


                $this->createDummyImage($profileImagePath);
                $this->createDummyImage($idImagePath);

                $student = Student::create([
                    'user_id' => $user->id,
                    'identification_image' => $profileImagePath,
                    'admission_year' => $admissionYear,
                    'joining_class' => $joiningClass,
                    'first_name' => $firstName,
                    'middle_name' => rand(0, 1) ? $middleNames[array_rand($middleNames)] : null,
                    'last_name' => $lastName,
                    'dob' => $dob,
                    'gender' => rand(0, 1) ? 'Male' : 'Female',
                    'citizenship' => json_encode($citizenships[array_rand($citizenships)]),
                    'id_type' => $idTypeValue,
                    'id_no' => strtoupper($idTypeValue) . '-' . rand(10000000, 99999999),
                    'id_image_path' => $idImagePath, 
                    'a_level_combination' => ($joiningClass === '5S' || $joiningClass === '6S') ? $this->getRandomCombination() : null,
                    'applying_section' => rand(0, 1) ? 'Day' : 'Boarding',
                    'religious_affiliation' => rand(0, 1) ? 'Christianity' : 'Islam',
                    'additional_info' => rand(0, 1) ? 'Student shows great potential in sciences and leadership.' : null,
                    'spoken_languages' => json_encode($languages[array_rand($languages)]),
                ]);

                // Academic History (1-3 records)
                $numAcademicRecords = rand(1, 3);
                for ($j = 0; $j < $numAcademicRecords; $j++) {
                    $academicLevelCases = AcademicLevel::cases();
                    $academicLevel = $academicLevelCases[array_rand($academicLevelCases)];
                    $fromYear = $admissionYear - ($numAcademicRecords - $j) * 2;
                    $toYear = $fromYear + rand(1, 3);

                    AcademicHistory::create([
                        'student_id' => $student->id,
                        'academic_level' => $academicLevel->value,
                        'school_name' => $schools[array_rand($schools)],
                        'from_year' => $fromYear,
                        'to_year' => $toYear,
                        'aggregate_score' => rand(10, 36),
                        'grade' => $this->getRandomGrade(),
                        'repeat_class' => rand(0, 10) > 8 ? 1 : 0,
                        'repeated_class' => rand(0, 10) > 8 ? rand(1, 4) : null,
                        'skip_class' => rand(0, 10) > 8 ? 1 : 0,
                        'skipped_class' => rand(0, 10) > 8 ? rand(1, 4) : null,
                    ]);
                }

                // Medical History
                $hasHealthIssues = rand(0, 10) < 3;
                MedicalHistory::create([
                    'student_id' => $student->id,
                    'has_health_issues' => $hasHealthIssues ? 1 : 0,
                    'health_issues' => $hasHealthIssues ? $this->getRandomHealthIssue() : null,
                ]);

                // Discipline History
                $hasDisciplinaryIssues = rand(0, 10) < 2;
                $disciplinaryAction = null;
                if ($hasDisciplinaryIssues) {
                    $actionCases = DisciplineAction::cases();
                    $disciplinaryAction = $actionCases[array_rand($actionCases)];
                }

                DisciplineHistory::create([
                    'student_id' => $student->id,
                    'has_disciplinary_issues' => $hasDisciplinaryIssues ? 1 : 0,
                    'disciplinary_issues' => $disciplinaryAction?->value,
                    'reason' => $hasDisciplinaryIssues ? $this->getRandomDisciplinaryReason() : null,
                ]);

                // Career Aspiration
                $aspirationCases = CareerAspirations::cases();
                $aspiration = $aspirationCases[array_rand($aspirationCases)];
                $subjects = Subjects::cases();
                shuffle($subjects);
                $subjectValues = array_column($subjects, 'value');

                CareerAspiration::create([
                    'student_id' => $student->id,
                    'aspiration' => $aspiration->value,
                    'best_done_subjects' => json_encode(array_slice($subjectValues, 0, 3)),
                    'worst_done_subjects' => json_encode(array_slice($subjectValues, 3, 3)),
                    'favorite_subjects' => json_encode(array_slice($subjectValues, 6, 3)),
                ]);
            });
        }

        $this->command->info('Successfully seeded 20 students with all related data!');
    }

    /**
     * Create a dummy JPEG image file
     */
    private function createDummyImage(string $path): void
    {
        $image = imagecreatetruecolor(100, 100);
        $bgColor = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
        imagefill($image, 0, 0, $bgColor);

        $textColor = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 5, 10, 40, 'DUMMY', $textColor);

        ob_start();
        imagejpeg($image, null, 90);
        $imageData = ob_get_clean();
        unset($image);

        Storage::disk('public')->put($path, $imageData);
    }

    private function getRandomCombination(): string
    {
        $combinations = ['PCM', 'PCB', 'MEC', 'HEG', 'LIT/DIV/ART', 'BCM', 'PEM'];
        return $combinations[array_rand($combinations)];
    }

    private function getRandomGrade(): string
    {
        $grades = ['A', 'B', 'C', 'D', 'E', 'F', '1st Grade', '2nd Grade', '3rd Grade'];
        return $grades[array_rand($grades)];
    }

    private function getRandomHealthIssue(): string
    {
        $issues = [
            'Asthma - requires inhaler during physical activities',
            'Allergic to peanuts and shellfish',
            'Type 1 Diabetes - requires insulin monitoring',
            'Epilepsy - controlled with medication',
            'Sickle cell trait - requires hydration monitoring',
        ];
        return $issues[array_rand($issues)];
    }

    private function getRandomDisciplinaryReason(): string
    {
        $reasons = [
            'Late arrival to school on multiple occasions',
            'Disruptive behavior during assembly',
            'Unauthorized use of mobile phone during class',
            'Dress code violation',
        ];
        return $reasons[array_rand($reasons)];
    }
}
