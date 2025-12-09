<?php

namespace App\Http\Controllers;

use App\Helpers\IDType;
use App\Models\AcademicHistory;
use App\Models\CareerAspiration;
use App\Models\DisciplineHistory;
use App\Models\MedicalHistory;
use App\Models\students;
use Illuminate\Http\Request;
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
            'user_id' => 'required|exists:users,id',
            'first_name' => 'required',
            'middle_name' => 'nullable',
            'last_name' => 'required',
            'dob' => 'required|date',
            'gender' => 'required|string|max:50',
            'id_type' => 'required|in:' . implode(',', array_column(IDType::cases(), 'value')),
            'id_no' => 'required|string|max:100|unique:students,id_no',
            'id_image_path' => 'required|image|max:2048',
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
        students::create($validated);
        return redirect()->route('students.index')->with('success', 'Student created successfully.');
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
