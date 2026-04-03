<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\Term;
use App\Helpers\ReportCardStatus;
use App\Models\ReportCard;
use App\Models\Student;
use App\Models\Classes;
use App\Models\GradingScale;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ReportCardController extends Controller
{
    protected $gradingService;

    public function __construct(GradingService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    public function index(Request $request)
    {
        $query = ReportCard::with(['student', 'generatedBy']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('generated_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('generated_at', '<=', $request->to_date);
        }

        $reportCards = $query->orderBy('generated_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $students = Student::orderBy('first_name')->get();
        $classes = Classes::orderBy('name')->get();
        $terms = Term::cases();
        $years = range(now()->year - 2, now()->year + 2);

        return view('report-cards.index', compact('reportCards', 'students', 'classes', 'terms', 'years'));
    }

    public function create()
    {
        $students = Student::with('class')->orderBy('first_name')->get();
        $terms = Term::cases();
        $years = range(now()->year - 2, now()->year + 2);

        return view('report-cards.create', compact('students', 'terms', 'years'));
    }

    public function store(Request $request)
    {
        Log::info('STORE REPORT CARD REACHED', $request->all());

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'term' => 'required|in:1,2,3',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();


            $reportCard = ReportCard::create([
                'student_id' => $validated['student_id'],
                'term' => $validated['term'],
                'year' => $validated['year'],
                'generated_by' => Auth::id(),
                'generated_at' => now(),
            ]);


            $pdf = $this->gradingService->generateReportCardPdf(
                $validated['student_id'],
                $validated['term'],
                $validated['year']
            );


            $student = Student::find($validated['student_id']);
            $filename = "report_card_{$student->admission_number}_T{$validated['term']}_{$validated['year']}.pdf";
            $path = "report-cards/{$filename}";
            Storage::put($path, $pdf->output());
            $reportCard->update(['pdf_path' => $path]);

            DB::commit();

            return redirect()->route('report-cards.show', $reportCard)
                ->with('success', 'Report card generated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('EXCEPTION', ['msg' => $th->getMessage()]);
            return back()->with('error', 'Failed to generate report card: ' . $th->getMessage())->withInput();
        }
    }

    public function show(ReportCard $reportCard)
    {
        $data = $this->gradingService->getReportCardData(
            $reportCard->student_id,
            $reportCard->term,
            $reportCard->year
        );

        return view('report-cards.show', array_merge(
            ['reportCard' => $reportCard],
            $data
        ));
    }

    public function download(ReportCard $reportCard)
    {
        try {
            if (!$reportCard->pdf_path || !Storage::exists($reportCard->pdf_path)) {
                $pdf = $this->gradingService->generateReportCardPdf(
                    $reportCard->student_id,
                    $reportCard->term,
                    $reportCard->year
                );
                $reportCard->update(['pdf_path' => $reportCard->pdf_path]);
            }

            return Storage::download($reportCard->pdf_path);
        } catch (\Throwable $th) {
            Log::error('DOWNLOAD ERROR', ['msg' => $th->getMessage()]);
            return back()->with('error', 'Failed to download report card: ' . $th->getMessage());
        }
    }

    public function destroy(ReportCard $reportCard)
    {
        try {
            if ($reportCard->pdf_path && Storage::exists($reportCard->pdf_path)) {
                Storage::delete($reportCard->pdf_path);
            }

            $reportCard->delete();

            return redirect()->route('report-cards.index')
                ->with('success', 'Report card deleted successfully.');
        } catch (\Throwable $th) {
            Log::error('DELETE ERROR', ['msg' => $th->getMessage()]);
            return back()->with('error', 'Failed to delete report card: ' . $th->getMessage());
        }
    }

    public function bulkGenerate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|in:1,2,3',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $students = Student::where('class_id', $validated['class_id'])->get();
            $generated = 0;
            $skipped = 0;

            foreach ($students as $student) {

                $exists = ReportCard::where('student_id', $student->id)
                    ->where('term', $validated['term'])
                    ->where('year', $validated['year'])
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }


                $pdf = $this->gradingService->generateReportCardPdf(
                    $student->id,
                    $validated['term'],
                    $validated['year']
                );

                $reportCard = ReportCard::create([
                    'student_id' => $student->id,
                    'generated_by' => Auth::id(),
                    'generated_at' => now(),
                ]);

                $filename = "report_card_{$student->admission_number}_T{$validated['term']}_{$validated['year']}.pdf";
                $path = "report-cards/{$filename}";
                Storage::put($path, $pdf->output());
                $reportCard->update(['pdf_path' => $path]);

                $generated++;
            }

            DB::commit();

            $message = "$generated report cards generated successfully.";
            if ($skipped > 0) {
                $message .= " $skipped students already had report cards.";
            }

            return redirect()->route('report-cards.index')
                ->with('success', $message);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('BULK GENERATE ERROR', ['msg' => $th->getMessage()]);
            return back()->with('error', 'Failed to generate report cards: ' . $th->getMessage());
        }
    }
}
