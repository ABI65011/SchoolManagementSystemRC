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

class StudentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = students::latest()->with(['user', 'academicHistories', 'disciplineHistory', 'medicalHistory', 'careerAspiration'])->paginate(10);
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
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // User validation mwahahahaha..
            "name" => "required",
            "email" => "required|unique:users,email",
            "role" => "required|in:" . implode(",", array_column(UserRoles::cases(), "value")),

            //Students validation
            // 'user_id' => 'required|exists:users,id',
            'admission_year' => 'required|integer',
            'joining_class' => 'required',
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'dob' => 'required|date',
            'gender' => 'required|string|max:50',
            'citizenship' => 'required|array',
            'id_type' => 'required|in:' . implode(',', array_column(IDType::cases(), 'value')),
            'id_no' => 'required|string|    max:100|unique:students,id_no',
            'id_image_path' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            'a_level_combination' => 'nullable',
            'applying_section' => 'required|in:' . implode(',', array_column(ApplyingSection::cases(), 'value')),
            'religious_affiliation' => 'required|in:' . implode(',', array_column(ReligiousAffiliation::cases(), 'value')),
            'other_religious_affiliation' => 'nullable',
            'has_aditional_info' => 'boolean',
            'additional_info' => 'nullable',
            'spoken_languages' => 'required|array',

            //Academic History validation
            'academic_history'          => 'required|array|min:1',
            'academic_history.0.students_id' => 'nullable|exists:students,id',
            'academic_history.0.academic_level' => 'required|in:' . implode(',', array_column(AcademicLevel::cases(), 'value')),
            'academic_history.0.school_name'    => 'required|string|max:200',
            'academic_history.0.from_year'      => 'required|integer',
            'academic_history.0.to_year'        => 'required|integer',
            'academic_history.0.aggregate_score' => 'required|numeric',
            'average_position' => 'nullable',
            'grade' => 'nullable',
            'ple_file' => 'nullable|string|max:2048',
            'o_level_file' => 'nullable|string|max:2048',
            'other_file' => 'nullable|string|max:2048',
            'repeat_class' => 'boolean',
            'repeated_class' => 'nullable',
            'skip_class' => 'boolean',
            'skipped_class' => 'nullable',

            //Medical History validation
            'has_health_issues' => 'boolean',
            'health_issues' => 'nullable',
            'files' => 'nullable|string|max:2048',

            //Discipline History validation
            'has_disciplinary_issues' => 'boolean',
            'disciplinary_issues' => 'nullable|in:' . implode(',', array_column(DisciplineAction::cases(), 'value')),
            'reason' => 'nullable',

            //Career Aspiration validation
            'aspiration' => 'nullable|in:' . implode(',', array_column(CareerAspirations::cases(), 'value')),
            'other_aspiration' => 'nullable',
            'best_done_subjects' => 'nullable|in:' . implode(',', array_column(Subjects::cases(), 'value')),
            'other_best_done_subjects' => 'nullable',
            'worst_done_subjects' => 'nullable|in:' . implode(',', array_column(Subjects::cases(), 'value')),
            'other_worst_done_subjects' => 'nullable',
            'favorite_subjects' => 'nullable|in:' . implode(',', array_column(Subjects::cases(), 'value')),
            'other_favorite_subjects' => 'nullable',
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
        $validated['password'] = Str::uuid();
        // dd($validated);

        try {
            DB::beginTransaction();

            $user = User::create($validated);
            $validated['user_id'] = $user->id;
            Log::info('ABOUT TO CREATE STUDENT');

            $idPath = $request->file('id_image_path')->store('identity', 'public');

            $plePath   = $request->hasFile('ple_file')   ? $request->file('ple_file')->store('academicFiles', 'public')   : null;
            $oLevelPath = $request->hasFile('o_level_file') ? $request->file('o_level_file')->store('academicFiles', 'public') : null;
            $otherPath = $request->hasFile('other_file') ? $request->file('other_file')->store('academicFiles', 'public') : null;
            $medicalPath = $request->hasFile('files')     ? $request->file('files')->store('medicalFiles', 'public')      : null;

            $validated['citizenship']        = json_encode($validated['citizenship']);
            $validated['spoken_languages']   = json_encode($validated['spoken_languages']);
            $validated['additional_info']    = $validated['additional_info'] ?? null;
            // $validated['admission_year'] = Carbon::createFromDate($validated['admission_year'], 1, 1);

            $student = students::create([
                'user_id'               => $validated['user_id'],
                'admission_year'        => $validated['admission_year'],
                'joining_class'         => $validated['joining_class'],
                'first_name'            => $validated['first_name'],
                'middle_name'           => $validated['middle_name']           ?? null,
                'last_name'             => $validated['last_name'],
                'dob'                   => $validated['dob'],
                'gender'                => $validated['gender'],
                'citizenship'           => $validated['citizenship'],
                'id_type'               => $validated['id_type'],
                'id_no'                 => $validated['id_no'],
                'id_image_path'         => $validated['id_image_path'],
                'a_level_combination'   => $validated['a_level_combination']   ?? null,
                'applying_section'      => $validated['applying_section'],
                'religious_affiliation' => $validated['religious_affiliation'],
                'additional_info'       => $validated['additional_info']       ?? null,
                'spoken_languages'      => $validated['spoken_languages'],
            ]);
            Log::info('STUDENT CREATED', ['id' => $student->id]);
            $validated['students_id'] = $student->id;

            $academic = $request->input('academic_history.0');
            AcademicHistory::create([
                'students_id'      => $student->id,
                'academic_level'  => $academic['academic_level'],
                'school_name'     => $academic['school_name'],
                'from_year'       => $academic['from_year'],
                'to_year'         => $academic['to_year'],
                'aggregate_score' => $academic['aggregate_score'],
                'grade'           => $academic['grade'] ?? null,
                'ple_file'        => $plePath  ?? null,
                'o_level_file'    => $oLevelPath ?? null,
                'other_file'      => $otherPath  ?? null,
            ]);
            MedicalHistory::create([
                'students_id'        => $student->id,
                'has_health_issues' => $validated['has_health_issues'] ?? 0,
                'health_issues'     => $validated['health_issues']     ?? null,
                'files'             => isset($medicalPath) ? json_encode([$medicalPath]) : null,
            ]);
            DisciplineHistory::create([
                'students_id'            => $student->id,
                'has_disciplinary_issues' => 1,
                'disciplinary_issues'   => $validated['disciplinary_issues'],
                'reason'                => $validated['reason'],
            ]);
            CareerAspiration::create([
                'students_id'         => $student->id,
                'aspiration'         => $validated['aspiration']         ?? null,
                'best_done_subjects' => $validated['best_done_subjects'] ?? null,
                'other_best_done_subjects' => $validated['other_best_done_subjects'] ?? null,
                'worst_done_subjects' => $validated['worst_done_subjects'] ?? null,
                'other_worst_done_subjects' => $validated['other_worst_done_subjects'] ?? null,
                'favorite_subjects' => $validated['favorite_subjects'] ?? null,
                'other_favorite_subjects' => $validated['other_favorite_subjects'] ?? null,
            ]);

            DB::commit();
            $user->syncRoles($validated['role']);

            $token = Password::createToken($user);
            // $user->sendPasswordResetNotification($token);

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully.');
        } catch (\Throwable $th) {
            Log::error('EXCEPTION INSIDE TRY', ['msg' => $th->getMessage(), 'trace' => $th->getTraceAsString()]);
            return back()->with('error', 'An error occurred while creating the student: ' . $th->getMessage())->withInput();
            DB::rollBack();


            $toDelete = array_filter([
                $validated['id_image_path'] ?? null,
                $validated['ple_file']          ?? null,
                $validated['o_level_file']      ?? null,
                $validated['other_file']        ?? null,
                $validated['files']             ?? null,
            ]);
            Storage::delete($toDelete);

            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()))
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(students $students)
    {
        return view('students.show', compact('students'));
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
            'are_they_same' => $student->id_no === $request->id_no
        ]);


        $validator = Validator::make($request->all(), [

            // STUDENT
            'user_id' => 'required|exists:users,id',
            'admission_year' => 'required',
            'joining_class' => 'required',
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'dob' => 'required|date',
            'gender' => 'required|string|max:50',
            'citizenship' => 'required|array',
            'id_type' => 'required|in:' . implode(',', array_column(IDType::cases(), 'value')),
            'id_no' => 'required|string|max:100|unique:students,id_no,' . $student->id,
            'id_image_path' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'a_level_combination' => 'nullable',
            'applying_section' => 'required|in:' . implode(',', array_column(ApplyingSection::cases(), 'value')),
            'religious_affiliation' => 'required|in:' . implode(',', array_column(ReligiousAffiliation::cases(), 'value')),
            'other_religious_affiliation' => 'nullable',
            'additional_info' => 'nullable',
            'spoken_languages' => 'required|array',

            // ACADEMIC
            'academic_history' => 'required|array|min:1',
            'academic_history.0.academic_level' => 'required|in:' . implode(',', array_column(AcademicLevel::cases(), 'value')),
            'academic_history.0.school_name' => 'required|string|max:200',
            'academic_history.0.from_year' => 'required|integer|min:1900|max:' . date('Y'),
            'academic_history.0.to_year' => 'required|integer|min:1900|max:' . date('Y'),
            'academic_history.0.aggregate_score' => 'required|numeric',
            'academic_history.0.grade' => 'nullable',

            'ple_file' => 'nullable|file|max:2048',
            'o_level_file' => 'nullable|file|max:2048',
            'other_file' => 'nullable|file|max:2048',

            //MEDICAL
            'has_health_issues' => 'boolean',
            'health_issues' => 'nullable',
            'files' => 'nullable|file|max:2048',

            //DISCIPLINE
            'has_disciplinary_issues' => 'boolean',
            'disciplinary_issues' => 'nullable|in:' . implode(',', array_column(DisciplineAction::cases(), 'value')),
            'reason' => 'nullable',

            //CAREER
            'aspiration' => 'nullable|in:' . implode(',', array_column(CareerAspirations::cases(), 'value')),
            'best_done_subjects' => 'nullable|in:' . implode(',', array_column(Subjects::cases(), 'value')),
            'other_best_done_subjects' => 'nullable',
            'worst_done_subjects' => 'nullable|in:' . implode(',', array_column(Subjects::cases(), 'value')),
            'other_worst_done_subjects' => 'nullable',
            'favorite_subjects' => 'nullable|in:' . implode(',', array_column(Subjects::cases(), 'value')),
            'other_favorite_subjects' => 'nullable',
        ]);

        Log::info('Validation Rule', [
            'rule' => 'unique:students,id_no,' . $student->id
        ]);

        if ($validator->fails()) {
            Log::warning('UPDATE VALIDATION FAILED', $validator->errors()->toArray());

            Log::warning('Validation Details', [
                'student_id' => $student->id,
                'input_id_no' => $request->id_no,
                'current_id_no' => $student->id_no
            ]);

            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            if ($request->hasFile('id_image_path')) {
                Storage::delete($student->id_image_path);
                $validated['id_image_path'] = $request->file('id_image_path')->store('identity', 'public');
            }

            $plePath = $request->hasFile('ple_file')
                ? $request->file('ple_file')->store('academicFiles', 'public')
                : null;

            $oLevelPath = $request->hasFile('o_level_file')
                ? $request->file('o_level_file')->store('academicFiles', 'public')
                : null;

            $otherPath = $request->hasFile('other_file')
                ? $request->file('other_file')->store('academicFiles', 'public')
                : null;

            $medicalPath = $request->hasFile('files')
                ? $request->file('files')->store('medicalFiles', 'public')
                : null;

            // STUDENT UPDATE
            $student->update([
                'user_id' => $validated['user_id'],
                'admission_year' => $validated['admission_year'],
                'joining_class' => $validated['joining_class'],
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name' => $validated['last_name'],
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'citizenship' => json_encode($validated['citizenship']),
                'id_type' => $validated['id_type'],
                'id_no' => $validated['id_no'],
                'a_level_combination' => $validated['a_level_combination'] ?? null,
                'applying_section' => $validated['applying_section'],
                'religious_affiliation' => $validated['religious_affiliation'],
                'additional_info' => $validated['additional_info'] ?? null,
                'spoken_languages' => json_encode($validated['spoken_languages']),
                'id_image_path' => $validated['id_image_path'] ?? $student->id_image_path,
            ]);

            // ACADEMIC UPDATE
            $academic = $student->academicHistories()->first();
            $row = $validated['academic_history'][0];

            $academic->update([
                'academic_level' => $row['academic_level'],
                'school_name' => $row['school_name'],
                'from_year' => $row['from_year'],
                'to_year' => $row['to_year'],
                'aggregate_score' => $row['aggregate_score'],
                'grade' => $row['grade'] ?? null,
                'ple_file' => $plePath ?? $academic->ple_file,
                'o_level_file' => $oLevelPath ?? $academic->o_level_file,
                'other_file' => $otherPath ?? $academic->other_file,
            ]);

            // MEDICAL UPDATE
            $student->medicalHistory()->update([
                'has_health_issues' => $validated['has_health_issues'] ?? 0,
                'health_issues' => $validated['health_issues'] ?? null,
                'files' => $medicalPath ? json_encode([$medicalPath]) : $student->medicalHistory->files,
            ]);

            // DISCIPLINE UPDATE
            $student->disciplineHistory()->update([
                'has_disciplinary_issues' => $validated['has_disciplinary_issues'] ?? 0,
                'disciplinary_issues' => $validated['disciplinary_issues'] ?? null,
                'reason' => $validated['reason'] ?? null,
            ]);

            // CAREER UPDATE
            $student->careerAspiration()->update([
                'aspiration' => $validated['aspiration'] ?? null,
                'best_done_subjects' => $validated['best_done_subjects'] ?? null,
                'other_best_done_subjects' => $validated['other_best_done_subjects'] ?? null,
                'worst_done_subjects' => $validated['worst_done_subjects'] ?? null,
                'other_worst_done_subjects' => $validated['other_worst_done_subjects'] ?? null,
                'favorite_subjects' => $validated['favorite_subjects'] ?? null,
                'other_favorite_subjects' => $validated['other_favorite_subjects'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Student updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('UPDATE FAILED', ['msg' => $th->getMessage()]);

            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()))
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
}
