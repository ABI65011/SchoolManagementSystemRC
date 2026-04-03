<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\GradingScale;
use App\Models\GradingScaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GradingScaleItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index(GradingScale $gradingScale)
    {
        $gradingScale->load('items');
        return view('grading-scale-items.index', compact('gradingScale'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(GradingScale $gradingScale)
    {
        $availableGrades = $gradingScale->getAvailableGradeCodes();
        $nextOrder = $gradingScale->items()->max('order') + 1;

        return view('grading-scale-items.create', compact('gradingScale', 'availableGrades', 'nextOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, GradingScale $gradingScale)
    {
        Log::info('STORE GRADING SCALE ITEM REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'grade_code' => 'required|string|max:10',
            'min_mark' => 'required|integer|min:0|max:100',
            'max_mark' => 'required|integer|min:0|max:100|gte:min_mark',
            'achievement_level' => 'nullable|string|max:100',
            'descriptor' => 'nullable|string|max:255',
            'points' => 'nullable|integer|min:0',
            'order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->with('validation', 'check the fields')->withErrors($validator->errors())->withInput();
        }

        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();
        $validated['grading_scale_id'] = $gradingScale->id;

        try {
            DB::beginTransaction();

            $existing = $gradingScale->items()
                ->where(function($q) use ($validated) {
                    $q->whereBetween('min_mark', [$validated['min_mark'], $validated['max_mark']])
                      ->orWhereBetween('max_mark', [$validated['min_mark'], $validated['max_mark']])
                      ->orWhere(function($q2) use ($validated) {
                          $q2->where('min_mark', '<=', $validated['min_mark'])
                             ->where('max_mark', '>=', $validated['max_mark']);
                      });
                })->exists();

            if ($existing) {
                DB::rollBack();
                return back()->with('error', 'Grade range overlaps with existing item.')->withInput();
            }

            $item = GradingScaleItem::create($validated);
            Log::info('Grading Scale Item CREATED', ['id' => $item->id, 'grade' => $item->grade_code]);

            DB::commit();
            return redirect()->route('grading-scales.show', $gradingScale)
                ->with('success', 'Grade item added successfully.');
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
    public function show(GradingScaleItem $gradingScaleItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GradingScaleItem $gradingScaleItem)
    {
        $gradingScale = $gradingScaleItem->gradingScale;
        $availableGrades = $gradingScale->getAvailableGradeCodes();

        return view('grading-scale-items.edit', compact('gradingScaleItem', 'gradingScale', 'availableGrades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GradingScaleItem $gradingScaleItem)
    {
        Log::info('UPDATE GRADING SCALE ITEM REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'grade_code' => 'required|string|max:10',
            'min_mark' => 'required|integer|min:0|max:100',
            'max_mark' => 'required|integer|min:0|max:100|gte:min_mark',
            'achievement_level' => 'nullable|string|max:100',
            'descriptor' => 'nullable|string|max:255',
            'points' => 'nullable|integer|min:0',
            'order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->with('validation', 'check the fields')->withErrors($validator->errors())->withInput();
        }

        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $existing = $gradingScaleItem->gradingScale->items()
                ->where('id', '!=', $gradingScaleItem->id)
                ->where(function ($q) use ($validated) {
                    $q->whereBetween('min_mark', [$validated['min_mark'], $validated['max_mark']])
                        ->orWhereBetween('max_mark', [$validated['min_mark'], $validated['max_mark']])
                        ->orWhere(function ($q2) use ($validated) {
                            $q2->where('min_mark', '<=', $validated['min_mark'])
                                ->where('max_mark', '>=', $validated['max_mark']);
                        });
                })->exists();

            if ($existing) {
                DB::rollBack();
                return back()->with('error', 'Grade range overlaps with existing item.')->withInput();
            }

            $gradingScaleItem->update($validated);
            Log::info('Grading Scale Item UPDATED', ['id' => $gradingScaleItem->id]);

            DB::commit();
            return redirect()->route('grading-scales.show', $gradingScaleItem->gradingScale)
                ->with('success', 'Grade item updated successfully.');
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
    public function destroy(GradingScaleItem $gradingScaleItem)
    {
        $gradingScale = $gradingScaleItem->gradingScale;

        try {
            $gradingScaleItem->delete();
            return redirect()->route('grading-scales.show', $gradingScale)
                ->with('success', 'Grade item deleted successfully.');
        } catch (\Throwable $th) {
            Log::error('DELETE ERROR', ['msg' => $th->getMessage()]);
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()));
        }
    }


    public function reorder(Request $request, GradingScale $gradingScale)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|exists:grading_scale_items,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($request->items as $item) {
                GradingScaleItem::where('id', $item['id'])
                    ->where('grading_scale_id', $gradingScale->id)
                    ->update(['order' => $item['order']]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('REORDER ERROR', ['msg' => $th->getMessage()]);
            return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
        }
    }
}
