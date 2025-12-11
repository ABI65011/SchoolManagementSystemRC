<?php

namespace App\Http\Controllers;

use App\Helpers\AcademicLevel;
use App\Helpers\AppHelper;
use App\Helpers\ApplyingSection;
use App\Helpers\CareerAspirations;
use App\Helpers\DisciplineAction;
use App\Helpers\IDType;
use App\Helpers\ReligiousAffiliation;
use App\Helpers\Subjects;
use App\Models\AcademicHistory;
use App\Models\CareerAspiration;
use App\Models\DisciplineHistory;
use App\Models\MedicalHistory;
use App\Models\students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return view('students.create', compact('academicHistories', 'careerAspiration', 'disciplineHistory', 'medicalHistory'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
            'id_image_path' => 'required|string|max:2048',
            'a_level_combination' => 'nullable',
            'applying_section' => 'required|in:' . implode(',', array_column( ApplyingSection::cases(), 'value')),
            'religious_affiliation' => 'required|in:' . implode(',', array_column( ReligiousAffiliation::cases(), 'value')),
            'other_religious_affiliation' => 'nullable',
            'has_aditional_info' => 'boolean',
            'additional_info' => 'nullable',
            'spoken_languages' => 'required|array',

            //Academic History validation
            'academic_level' => 'required|in:' . implode(',', array_column( AcademicLevel::cases(), 'value')),
            'other_academic_level' => 'nullable',
            'school_name' => 'required',
            'from_year' => 'required',
            'to_year' => 'required',
            'aggregate_score' => 'required',
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
            'disciplinary_issues' => 'nullable|in:' . implode(',', array_column( DisciplineAction::cases(), 'value')),
            'reason' => 'nullable',

            //Career Aspiration validation
            'aspiration' => 'nullable|in:' . implode(',', array_column( CareerAspirations::cases(), 'value')),
            'other_aspiration' => 'nullable',
            'best_done_subjects' => 'nullable|in:' . implode(',', array_column( Subjects::cases(), 'value')),
            'other_best_done_subjects' => 'nullable',
            'worst_done_subjects' => 'nullable|in:' . implode(',', array_column( Subjects::cases(), 'value')),
            'other_worst_done_subjects' => 'nullable',
            'favorite_subjects' => 'nullable|in:' . implode(',', array_column( Subjects::cases(), 'value')),
            'other_favorite_subjects' => 'nullable',
        ]);

        if ($validator->fails()) {
            return back()
                ->with('validation', 'Check the fields')
                ->withErrors($validator->errors())
                ->withInput();
        }

        $validated = $validator->validated();
        dd($validated);

        try {
            DB::beginTransaction();

            $students = students::create($validated);
            $validated['student_id'] = $students->id;

            $academicHistory = AcademicHistory::create($validated);
            $medicalHistory = MedicalHistory::create($validated);
            $disciplineHistory = DisciplineHistory::create($validated);
            $careerAspiration = CareerAspiration::create($validated);

            DB::commit();
            $validated['id_image_path'] = $request->file('id_image_path')->store('identity', 'public');

            $validated['ple_file'] = $request->file('ple_file')->store('academicFiles', 'public');

            $validated['o_level_file'] = $request->file('o_level_file')->store('academicFiles', 'public');

            $validated['other_file'] = $request->file('other_file')->store('academicFiles', 'public');

            $validated['files'] = $request->file('files')->store('medicalFiles', 'public');

            return redirect()->route('students.index')->with('success', 'Student created successfully.');

        } catch (\Throwable $th) {
            DB::rollBack();
            Storage::delete([
                $validated['id_image_path'] ?? null,
                $validated['ple_file'] ?? null,
                $validated['o_level_file'] ?? null,
                $validated['other_file'] ?? null,
                $validated['files'] ?? null,
            ]);
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()))->withInput();
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
