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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
        \Log::info('STORE ROUTE REACHED', $request->all());
        \Log::info('VALIDATION START');
        $validator = Validator::make($request->all(), [
            //Students validation
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
            'academic_history.0.from_year'      => 'required|integer|min:1900|max:' . date('Y'),
            'academic_history.0.to_year'        => 'required|integer|min:1900|max:' . date('Y'),
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
            \Log::info('VALIDATION FAILED', $validator->errors()->toArray());
            return back()
                ->with('validation', 'Check the fields')
                ->withErrors($validator->errors())
                ->withInput();
        }
        \Log::info('VALIDATION PASSED');

        $validated = $validator->validated();
        // dd($validated);

        try {
            DB::beginTransaction();
            \Log::info('ABOUT TO CREATE STUDENT');

            $idPath = $request->file('id_image_path')->store('identity', 'public');

            $plePath   = $request->hasFile('ple_file')   ? $request->file('ple_file')->store('academicFiles', 'public')   : null;
            $oLevelPath = $request->hasFile('o_level_file') ? $request->file('o_level_file')->store('academicFiles', 'public') : null;
            $otherPath = $request->hasFile('other_file') ? $request->file('other_file')->store('academicFiles', 'public') : null;
            $medicalPath = $request->hasFile('files')     ? $request->file('files')->store('medicalFiles', 'public')      : null;

            $validated['citizenship']        = json_encode($validated['citizenship']);
            $validated['spoken_languages']   = json_encode($validated['spoken_languages']);
            $validated['additional_info']    = $validated['additional_info'] ?? null;
            $validated['admission_year'] = Carbon::createFromDate($validated['admission_year'], 1, 1);

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
            \Log::info('STUDENT CREATED', ['id' => $student->id]);
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

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully.');
        } catch (\Throwable $th) {
            \Log::error('EXCEPTION INSIDE TRY', ['msg' => $th->getMessage(), 'trace' => $th->getTraceAsString()]);
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
    public function edit(students $students)
    {
        return view('students.edit', compact('students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, students $students)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string|max:50',
            'id_type' => 'required|string|max:100',
            'id_no' => 'required|string|max:100|unique:students,id_no,' . $students->id,
            'id_image_path' => 'required|string|max:255',
            'spoken_languages' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $validated = $validator->validated();
        if ($request->hasFile('id_image_path')) {
            $validated['id_image_path'] = $request->file('id_image_path')->store('identity', 'public');
        }
        $students->update($validated);
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
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
