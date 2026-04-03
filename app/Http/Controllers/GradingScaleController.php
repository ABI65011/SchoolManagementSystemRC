<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\GradingScaleName;
use App\Helpers\GradingScaleType;
use App\Models\GradingScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GradingScaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GradingScale::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $scales = $query->withCount('items')
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        if ($scales->isEmpty()) {
            Log::info('No grading scales found');
        } else {
        Log::info('Found ' . $scales->count() . ' grading scales');
        }
        return view('grading-scales.index', compact('scales'));

        // $scales = DB::table('grading_scales')
        //     ->get();

        // dd($scales);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = GradingScaleType::cases();
        $names = GradingScaleName::cases();

        return view('grading-scales.create', compact('types', 'names'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('STORE GRADING SCALE REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|in:' . implode(',', array_column(GradingScaleName::cases(), 'value')),
            'type' => 'required|in:' . implode(',', array_column(GradingScaleType::cases(), 'value')),
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->with('validation', 'check the fields')->withErrors($validator->errors())->withInput();
        }

        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->boolean('is_active');

        try {
            DB::beginTransaction();

            
            if ($validated['is_default']) {
                GradingScale::where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $scale = GradingScale::create($validated);
            Log::info('Grading Scale CREATED', ['id' => $scale->id, 'name' => $scale->name]);

            DB::commit();
            return redirect()->route('grading-scales.show', $scale)
                ->with('success', 'Grading scale created successfully. Add grade items now.');
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
    public function show(GradingScale $gradingScale)
    {
        $gradingScale->load(['items' => function ($q) {
            $q->orderBy('order');
        }]);

        return view('grading-scales.show', compact('gradingScale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GradingScale $gradingScale)
    {
        $types = GradingScaleType::cases();
        $names = GradingScaleName::cases();

        return view('grading-scales.edit', compact('gradingScale', 'types', 'names'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GradingScale $gradingScale)
    {
        Log::info('UPDATE GRADING SCALE REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|in:' . implode(',', array_column(GradingScaleName::cases(), 'value')),
            'type' => 'required|in:' . implode(',', array_column(GradingScaleType::cases(), 'value')),
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->with('validation', 'check the fields')->withErrors($validator->errors())->withInput();
        }

        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->boolean('is_active');

        try {
            DB::beginTransaction();

            if ($validated['is_default']) {
                GradingScale::where('is_default', true)
                    ->where('id', '!=', $gradingScale->id)
                    ->update(['is_default' => false]);
            }

            $gradingScale->update($validated);
            Log::info('Grading Scale UPDATED', ['id' => $gradingScale->id, 'name' => $gradingScale->name]);

            DB::commit();
            return redirect()->route('grading-scales.show', $gradingScale)
                ->with('success', 'Grading scale updated successfully.');
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
    public function destroy(GradingScale $gradingScale)
    {
        if ($gradingScale->exams()->exists()) {
            return back()->with('error', 'Cannot delete grading scale with associated exams.');
        }

        if ($gradingScale->reportCards()->exists()) {
            return back()->with('error', 'Cannot delete grading scale with associated report cards.');
        }

        try {
            DB::beginTransaction();
            $gradingScale->items()->delete();
            $gradingScale->delete();

            DB::commit();
            return redirect()->route('grading-scales.index')
                ->with('success', 'Grading scale deleted successfully.');
        } catch (\Throwable $th) {
            Log::error('DELETE ERROR', ['msg' => $th->getMessage()]);
            DB::rollBack();
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()));
        }
    }

    public function setDefault(GradingScale $gradingScale)
    {
        try {
            DB::beginTransaction();

            GradingScale::where('is_default', true)
                ->update(['is_default' => false]);

            $gradingScale->update(['is_default' => true]);

            DB::commit();
            return redirect()->route('grading-scales.index')
                ->with('success', 'Default grading scale updated.');
        } catch (\Throwable $th) {
            Log::error('SET DEFAULT ERROR', ['msg' => $th->getMessage()]);
            DB::rollBack();
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()));
        }
    }
}
