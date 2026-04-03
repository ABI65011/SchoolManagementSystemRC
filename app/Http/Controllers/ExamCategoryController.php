<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\ExamType;
use App\Models\ExamCategory;
use App\Models\GradingScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExamCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExamCategory::with(['mainCategory', 'gradingScale']);

        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $categories = $query->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('exam-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $mainCategories = ExamCategory::mainCategories()
            ->active()
            ->get();

        $gradingScales = GradingScale::active()->get();
        $examTypes = ExamType::options();

        return view('exam-categories.create', compact('mainCategories', 'gradingScales', 'examTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('STORE EXAM CATEGORY REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'main_category_id' => 'nullable|exists:exam_categories,id',
            'exam_type' => 'required|in:internal,external',
            'description' => 'nullable|string',
            'grading_scale_id' => 'nullable|exists:grading_scales,id',
            'requires_continuous_assessment' => 'boolean',
            'weight' => 'nullable|numeric|min:0|max:10',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->with('validation', 'check the fields')->withErrors($validator->errors())->withInput();
        }

        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();
        $validated['requires_continuous_assessment'] = $request->boolean('requires_continuous_assessment');
        $validated['is_active'] = $request->boolean('is_active', true);

        try {
            DB::beginTransaction();

            $category = ExamCategory::create($validated);
            Log::info('Exam Category CREATED', ['id' => $category->id, 'name' => $category->name]);

            DB::commit();
            return redirect()->route('exam-categories.index')
                ->with('success', 'Exam category created successfully.');
        } catch (\Throwable $th) {
            Log::error('EXCEPTION INSIDE TRY', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            DB::rollBack();
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()))->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamCategory $examCategory)
    {
        $examCategory->load(['mainCategory', 'subCategories', 'gradingScale', 'exams' => function ($q) {
            $q->latest()->limit(10);
        }]);

        return view('exam-categories.show', compact('examCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamCategory $examCategory)
    {

        $mainCategories = ExamCategory::mainCategories()
            ->active()
            ->where('id', '!=', $examCategory->id)
            ->get();

        $gradingScales = GradingScale::active()->get();
        $examTypes = ExamType::options();

        return view('exam-categories.edit', compact('examCategory', 'mainCategories', 'gradingScales', 'examTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamCategory $examCategory)
    {
        Log::info('UPDATE EXAM CATEGORY REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'main_category_id' => 'nullable|exists:exam_categories,id',
            'exam_type' => 'required|in:internal,external',
            'description' => 'nullable|string',
            'grading_scale_id' => 'nullable|exists:grading_scales,id',
            'requires_continuous_assessment' => 'boolean',
            'weight' => 'nullable|numeric|min:0|max:10',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->with('validation', 'check the fields')->withErrors($validator->errors())->withInput();
        }

        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();
        $validated['requires_continuous_assessment'] = $request->boolean('requires_continuous_assessment');
        $validated['is_active'] = $request->boolean('is_active', true);

        if (isset($validated['main_category_id']) && $validated['main_category_id'] == $examCategory->id) {
            return back()->with('error', 'A category cannot be its own main category.')->withInput();
        }

        try {
            DB::beginTransaction();

            $examCategory->update($validated);
            Log::info('Exam Category UPDATED', ['id' => $examCategory->id, 'name' => $examCategory->name]);

            DB::commit();
            return redirect()->route('exam-categories.show', $examCategory)
                ->with('success', 'Exam category updated successfully.');
        } catch (\Throwable $th) {
            Log::error('EXCEPTION INSIDE TRY', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            DB::rollBack();
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()))->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamCategory $examCategory)
    {
        if ($examCategory->subCategories()->exists()) {
            return back()->with('error', 'Cannot delete category with sub-categories.');
        }

        if ($examCategory->exams()->exists()) {
            return back()->with('error', 'Cannot delete category with associated exams.');
        }

        try {
            $examCategory->delete();
            return redirect()->route('exam-categories.index')
                ->with('success', 'Exam category deleted successfully.');
        } catch (\Throwable $th) {
            Log::error('DELETE ERROR', ['msg' => $th->getMessage()]);
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()));
        }
    }
}
