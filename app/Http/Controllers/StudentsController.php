<?php

namespace App\Http\Controllers;

use App\Helpers\AcademicLevel;
use App\Helpers\AppHelper;
use App\Helpers\ApplyingSection;
use App\Helpers\CareerAspirations;
use App\Helpers\DisciplineAction;
use App\Helpers\Gender;
use App\Helpers\IDType;
use App\Helpers\ReligiousAffiliation;
use App\Helpers\Subjects;
use App\Helpers\UserRoles;
use App\Models\AcademicHistory;
use App\Models\CareerAspiration;
use App\Models\DisciplineHistory;
use App\Models\MedicalHistory;
use App\Models\students;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = students::with(['user', 'academicHistories', 'disciplineHistory', 'medicalHistory', 'careerAspiration'])->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academicHistories = AcademicHistory::all();
        $careerAspiration = CareerAspiration::all();
        $disciplineHistory = DisciplineHistory::all();
        $medicalHistory = MedicalHistory::all();
        $users = User::whereDoesntHave('student')->get();
        return view('students.create',  compact('academicHistories', 'careerAspiration', 'disciplineHistory', 'medicalHistory', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('STORE ROUTE REACHED', $request->all());
        Log::info('VALIDATION START');

        $validator = Validator::make($request->all(), [
            // user
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', new Enum(UserRoles::class)],
            'password' => 'required|min:6',

            // student
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'dob' => 'required|date',
            'gender' => 'required|string|max:50',
            'citizenship' => 'required|array',
            'citizenship.*' => 'string',
            'religious_affiliation' => 'required|in:' . implode(',', array_column(ReligiousAffiliation::cases(), 'value')),
            'spoken_languages' => 'required|array',
            'spoken_languages.*' => 'string',

            'admission_year' => 'required|integer',
            'joining_class' => 'required|string|max:50',
            'a_level_combination' => 'nullable|string|max:50',
            'applying_section' => 'required|in:' . implode(',', array_column(ApplyingSection::cases(), 'value')),
            'id_type' => 'required|in:' . implode(',', array_column(IDType::cases(), 'value')),
            'id_no' => 'required|string|max:100|unique:students,id_no',
            'id_image_path' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'identification_image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',

            // ACADEMIC HISTORY
            'academic_history' => 'required|array|min:1',
            'academic_history.*.academic_level' => 'required|in:' . implode(',', array_column(AcademicLevel::cases(), 'value')),
            'academic_history.*.school_name' => 'required|string|max:200',
            'academic_history.*.from_year' => 'required|integer',
            'academic_history.*.to_year' => 'required|integer',
            'academic_history.*.aggregate_score' => 'required|numeric',
            'academic_history.*.grade' => 'nullable|string|max:50',
            'academic_history.*.ple_file' => 'nullable|file|max:2048',
            'academic_history.*.o_level_file' => 'nullable|file|max:2048',
            'academic_history.*.other_file' => 'nullable|file|max:2048',
            'academic_history.*.repeat_class' => 'nullable|boolean',
            'academic_history.*.repeated_class' => 'nullable|string|max:50',
            'academic_history.*.skip_class' => 'nullable|boolean',
            'academic_history.*.skipped_class' => 'nullable|string|max:50',

            // MEDICAL HISTORY
            'has_health_issues' => 'nullable|boolean',
            'health_issues' => 'nullable|string',
            'medical_files' => 'nullable|array',
            'medical_files.*' => 'file|max:2048',

            // DISCIPLINE HISTORY
            'has_disciplinary_issues' => 'nullable|boolean',
            'disciplinary_issues' => 'nullable|in:' . implode(',', array_column(DisciplineAction::cases(), 'value')),
            'reason' => 'nullable|string',

            // CAREER ASPIRATION
            'aspiration' => 'required|in:' . implode(',', array_column(CareerAspirations::cases(), 'value')),
            'best_done_subjects' => 'required|array',
            'best_done_subjects.*' => 'in:' . implode(',', array_column(Subjects::cases(), 'value')),
            'worst_done_subjects' => 'required|array',
            'worst_done_subjects.*' => 'in:' . implode(',', array_column(Subjects::cases(), 'value')),
            'favorite_subjects' => 'required|array',
            'favorite_subjects.*' => 'in:' . implode(',', array_column(Subjects::cases(), 'value')),

            'additional_info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::info('VALIDATION FAILED', $validator->errors()->toArray());
            return back()
                ->with('validation', 'Check the fields')
                ->withErrors($validator->errors())
                ->withInput();
        }

        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            // create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            Log::info('USER CREATED', ['id' => $user->id, 'email' => $user->email]);

            // handle files
            $idPath = $request->hasFile('id_image_path')
                ? $request->file('id_image_path')->store('identity', 'public')
                : null;

            $identification_image = $request->hasFile('identification_image')
                ? $request->file('identification_image')->store('profile', 'public')
                : null;

            // encode array for json storage
            $validated['citizenship'] = json_encode($validated['citizenship']);
            $validated['spoken_languages'] = json_encode($validated['spoken_languages']);
            $validated['best_done_subjects'] = json_encode($validated['best_done_subjects']);
            $validated['worst_done_subjects'] = json_encode($validated['worst_done_subjects']);
            $validated['favorite_subjects'] = json_encode($validated['favorite_subjects']);

            // create student
            $student = students::create([
                'user_id' => $user->id,
                'identification_image' => $identification_image ?? null,
                'admission_year' => $validated['admission_year'],
                'joining_class' => $validated['joining_class'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'citizenship' => $validated['citizenship'],
                'id_type' => $validated['id_type'],
                'id_no' => $validated['id_no'],
                'id_image_path' => $idPath ?? null,
                'a_level_combination' => $validated['a_level_combination'] ?? null,
                'applying_section' => $validated['applying_section'],
                'religious_affiliation' => $validated['religious_affiliation'],
                'additional_info' => $validated['additional_info'] ?? null,
                'spoken_languages' => $validated['spoken_languages'],
            ]);

            Log::info('STUDENT CREATED', ['id' => $student->id]);

            // create academic histories
            $academicHistories = $request->input('academic_history', []);
            foreach ($academicHistories as $index => $academic) {
                $academicFiles = $request->file("academic_history.{$index}") ?? [];

                $plePath = isset($academicFiles['ple_file'])
                    ? $academicFiles['ple_file']->store('academicFiles', 'public')
                    : null;

                $oLevelPath = isset($academicFiles['o_level_file'])
                    ? $academicFiles['o_level_file']->store('academicFiles', 'public')
                    : null;

                $otherPath = isset($academicFiles['other_file'])
                    ? $academicFiles['other_file']->store('academicFiles', 'public')
                    : null;

                AcademicHistory::create([
                    'students_id' => $student->id,
                    'academic_level' => $academic['academic_level'],
                    'school_name' => $academic['school_name'],
                    'from_year' => $academic['from_year'],
                    'to_year' => $academic['to_year'],
                    'aggregate_score' => $academic['aggregate_score'],
                    'grade' => $academic['grade'] ?? null,
                    'ple_file' => $plePath ?? null,
                    'o_level_file' => $oLevelPath ?? null,
                    'other_file' => $otherPath ?? null,
                    'repeat_class' => $academic['repeat_class'] ?? 0,
                    'repeated_class' => $academic['repeated_class'] ?? null,
                    'skip_class' => $academic['skip_class'] ?? 0,
                    'skipped_class' => $academic['skipped_class'] ?? null,
                ]);
            }

            // Medical
            $medicalPaths = [];
            if ($request->hasFile('medical_files')) {
                foreach ($request->file('medical_files') as $medicalFile) {
                    $medicalPaths[] = $medicalFile->store('medicalFiles', 'public');
                }
            }

            MedicalHistory::create([
                'students_id' => $student->id,
                'has_health_issues' => $validated['has_health_issues'] ?? 0,
                'health_issues' => $validated['health_issues'] ?? null,
                'files' => !empty($medicalPaths) ? json_encode($medicalPaths) : null,
            ]);

            // create discipline history
            DisciplineHistory::create([
                'students_id' => $student->id,
                'has_disciplinary_issues' => $validated['has_disciplinary_issues'] ?? 0,
                'disciplinary_issues' => $validated['disciplinary_issues'] ?? null,
                'reason' => $validated['reason'] ?? null,
            ]);

            // create career aspiration
            CareerAspiration::create([
                'students_id' => $student->id,
                'aspiration' => $validated['aspiration'],
                'best_done_subjects' => $validated['best_done_subjects'],
                'worst_done_subjects' => $validated['worst_done_subjects'],
                'favorite_subjects' => $validated['favorite_subjects'],
            ]);

            DB::commit();

            $user->syncRoles($validated['role']);

            // $token = Password::createToken($user);
            // $user->sendPasswordResetNotification($token);

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully. Default password is "password"');
        } catch (\Throwable $th) {
            Log::error('EXCEPTION INSIDE TRY', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            DB::rollBack();
            if (isset($idPath) && $idPath) {
                Storage::disk('public')->delete($idPath);
            }
            if (isset($identification_image) && $identification_image) {
                Storage::disk('public')->delete($identification_image);
            }

            return back()
                ->with('error', AppHelper::buildExceptionMessage($th->getMessage()))
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(students $student)
    {
        $users = User::whereHas('student')->get();
        $student->load([
            'academicHistories',
            'medicalHistory',
            'disciplineHistory',
            'careerAspiration',
            'user'
        ]);
        return view('profile_management.index', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(students $student)
    {
        $users = User::whereHas('student')->get();
        $student->load([
            'academicHistories',
            'medicalHistory',
            'disciplineHistory',
            'careerAspiration',
            'user'
        ]);
        return view('students.edit', compact('student', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, students $student)
    {
        Log::info('UPDATE ROUTE REACHED', $request->all());
        Log::info('Student Object', [
            'student_id' => $student->id,
            'current_id_no' => $student->id_no,
            'request_id_no' => $request->id_no,
        ]);

        $validator = Validator::make($request->all(), [

            'user_id' => 'nullable|exists:users,id',

            // STUDENT
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'dob' => 'required|date',
            'gender' => 'required|string|max:50',
            'citizenship' => 'required|array',
            'religious_affiliation' => 'required|in:' . implode(',', array_column(ReligiousAffiliation::cases(), 'value')),
            'spoken_languages' => 'required|array',


            'admission_year' => 'required|integer',
            'joining_class' => 'required|string|max:50',
            'a_level_combination' => 'nullable|string|max:50',
            'applying_section' => 'required|in:' . implode(',', array_column(ApplyingSection::cases(), 'value')),
            'id_type' => 'required|in:' . implode(',', array_column(IDType::cases(), 'value')),
            'id_no' => 'required|string|max:100|unique:students,id_no,' . $student->id,
            'id_image_path' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'identification_image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',

            // ACADEMIC HISTORY
            'academic_history' => 'required|array|min:1',
            'academic_history.*.academic_level' => 'required|in:' . implode(',', array_column(AcademicLevel::cases(), 'value')),
            'academic_history.*.school_name' => 'required|string|max:200',
            'academic_history.*.from_year' => 'required|integer',
            'academic_history.*.to_year' => 'required|integer',
            'academic_history.*.aggregate_score' => 'required|numeric',
            'academic_history.*.grade' => 'nullable|string|max:50',
            'academic_history.*.ple_file' => 'nullable|file|max:2048',
            'academic_history.*.o_level_file' => 'nullable|file|max:2048',
            'academic_history.*.other_file' => 'nullable|file|max:2048',
            'academic_history.*.repeat_class' => 'nullable|boolean',
            'academic_history.*.repeated_class' => 'nullable|string|max:50',
            'academic_history.*.skip_class' => 'nullable|boolean',
            'academic_history.*.skipped_class' => 'nullable|string|max:50',

            // MEDICAL HISTORY
            'has_health_issues' => 'nullable|boolean',
            'health_issues' => 'nullable|string',
            'medical_files' => 'nullable|array',
            'medical_files.*' => 'file|max:2048',

            // DISCIPLINE HISTORY
            'has_disciplinary_issues' => 'nullable|boolean',
            'disciplinary_issues' => 'nullable|in:' . implode(',', array_column(DisciplineAction::cases(), 'value')),
            'reason' => 'nullable|string',

            // CAREER ASPIRATION
            'aspiration' => 'required|in:' . implode(',', array_column(CareerAspirations::cases(), 'value')),
            'best_done_subjects' => 'required|array',
            'worst_done_subjects' => 'required|array',
            'favorite_subjects' => 'required|array',

            'additional_info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning('UPDATE VALIDATION FAILED', $validator->errors()->toArray());
            return back()
                ->with('validation', 'Check the fields')
                ->withErrors($validator->errors())
                ->withInput();
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            // files
            if ($request->hasFile('id_image_path')) {
                if ($student->id_image_path) {
                    Storage::disk('public')->delete($student->id_image_path);
                }
                $idPath = $request->file('id_image_path')->store('identity', 'public');
            } else {
                $idPath = $student->id_image_path;
            }

            if ($request->hasFile('identification_image')) {
                if ($student->identification_image) {
                    Storage::disk('public')->delete($student->identification_image);
                }
                $identification_image = $request->file('identification_image')->store('profile', 'public');
            } else {
                $identification_image = $student->identification_image;
            }

            // encode arrays for json storage
            $validated['citizenship'] = json_encode($validated['citizenship']);
            $validated['spoken_languages'] = json_encode($validated['spoken_languages']);
            $validated['best_done_subjects'] = json_encode($validated['best_done_subjects']);
            $validated['worst_done_subjects'] = json_encode($validated['worst_done_subjects']);
            $validated['favorite_subjects'] = json_encode($validated['favorite_subjects']);

            // UPDATE STUDENT
            $student->update([
                'user_id' => $validated['user_id'] ?? $student->user_id,
                'identification_image' => $identification_image,
                'admission_year' => $validated['admission_year'],
                'joining_class' => $validated['joining_class'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'citizenship' => $validated['citizenship'],
                'id_type' => $validated['id_type'],
                'id_no' => $validated['id_no'],
                'id_image_path' => $idPath,
                'a_level_combination' => $validated['a_level_combination'] ?? null,
                'applying_section' => $validated['applying_section'],
                'religious_affiliation' => $validated['religious_affiliation'],
                'additional_info' => $validated['additional_info'] ?? null,
                'spoken_languages' => $validated['spoken_languages'],
            ]);

            Log::info('STUDENT UPDATED', ['id' => $student->id]);

            // UPDATE ACADEMIC HISTORIES
            $student->academicHistories()->delete();

            $academicHistories = $request->input('academic_history', []);
            foreach ($academicHistories as $index => $academic) {
                $academicFiles = $request->file("academic_history.{$index}") ?? [];

                $plePath = isset($academicFiles['ple_file'])
                    ? $academicFiles['ple_file']->store('academicFiles', 'public')
                    : null;

                $oLevelPath = isset($academicFiles['o_level_file'])
                    ? $academicFiles['o_level_file']->store('academicFiles', 'public')
                    : null;

                $otherPath = isset($academicFiles['other_file'])
                    ? $academicFiles['other_file']->store('academicFiles', 'public')
                    : null;

                AcademicHistory::create([
                    'students_id' => $student->id,
                    'academic_level' => $academic['academic_level'],
                    'school_name' => $academic['school_name'],
                    'from_year' => $academic['from_year'],
                    'to_year' => $academic['to_year'],
                    'aggregate_score' => $academic['aggregate_score'],
                    'grade' => $academic['grade'] ?? null,
                    'ple_file' => $plePath ?? null,
                    'o_level_file' => $oLevelPath ?? null,
                    'other_file' => $otherPath ?? null,
                    'repeat_class' => $academic['repeat_class'] ?? 0,
                    'repeated_class' => $academic['repeated_class'] ?? null,
                    'skip_class' => $academic['skip_class'] ?? 0,
                    'skipped_class' => $academic['skipped_class'] ?? null,
                ]);
            }

            // Handle Medical
            $medicalPaths = [];
            if ($request->hasFile('medical_files')) {
                $oldMedicalHistory = $student->medicalHistory;
                if ($oldMedicalHistory && $oldMedicalHistory->files) {
                    $oldFiles = json_decode($oldMedicalHistory->files, true) ?: [];
                    foreach ($oldFiles as $oldFile) {
                        Storage::disk('public')->delete($oldFile);
                    }
                }

                foreach ($request->file('medical_files') as $medicalFile) {
                    $medicalPaths[] = $medicalFile->store('medicalFiles', 'public');
                }
            }

            MedicalHistory::updateOrCreate(
                ['students_id' => $student->id],
                [
                    'has_health_issues' => $validated['has_health_issues'] ?? 0,
                    'health_issues' => $validated['health_issues'] ?? null,
                    'files' => !empty($medicalPaths) ? json_encode($medicalPaths) : ($student->medicalHistory->files ?? null),
                ]
            );


            DisciplineHistory::updateOrCreate(
                ['students_id' => $student->id],
                [
                    'has_disciplinary_issues' => $validated['has_disciplinary_issues'] ?? 0,
                    'disciplinary_issues' => $validated['disciplinary_issues'] ?? null,
                    'reason' => $validated['reason'] ?? null,
                ]
            );


            CareerAspiration::updateOrCreate(
                ['students_id' => $student->id],
                [
                    'aspiration' => $validated['aspiration'],
                    'best_done_subjects' => $validated['best_done_subjects'],
                    'worst_done_subjects' => $validated['worst_done_subjects'],
                    'favorite_subjects' => $validated['favorite_subjects'],
                ]
            );

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Student updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('UPDATE FAILED', ['msg' => $th->getMessage()]);

            return back()
                ->with('error', AppHelper::buildExceptionMessage($th->getMessage()))
                ->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(students $students)
    {
        $students->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    // StudentController.php
    // public function print(students $student)
    // {
    //     $student->load(['user', 'academicHistories', 'medicalHistory', 'disciplineHistory', 'careerAspiration']);
    //     return view('students.print', compact('student'));
    // }
}
