<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\HolidayType;
use App\Helpers\RecurringPattern;
use App\Models\holidayCalendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HolidayCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $total = HolidayCalendar::count();
        $publicHolidays = HolidayCalendar::where('type', 'Public Holiday')->count();
        $schoolHolidays = HolidayCalendar::where('type', 'School Holiday')->count();
        $religiousHolidays = HolidayCalendar::where('type', 'Religious Holiday')->count();
        $nationalHolidays = HolidayCalendar::where('type', 'National Holiday')->count();
        $otherHolidays = HolidayCalendar::where('type', 'Other Holiday')->count();
        $holidays = HolidayCalendar::orderBy('date', 'asc')->get();
        return view('holiday_calendars.index', compact('holidays', 'total', 'publicHolidays', 'schoolHolidays', 'religiousHolidays', 'nationalHolidays', 'otherHolidays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('holiday_calendars.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('STORE ROUTE REACHED', $request->all());
        Log::info('VALIDATION START');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:' . implode(',', array_column(HolidayType::cases(), 'value')),
            'is_recurring' => 'boolean',
            'recurring_pattern' => 'required_if:is_recurring,1|in:' . implode(',', array_column(RecurringPattern::cases(), 'value')),
            'recurring_rules' => 'nullable|json',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return back()->with('validation', 'check the fields')->withErrors($validator->errors())->withInput();
        }

        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();
        if ($request->is_recurring && $request->recurring_pattern !== 'none') {
            $validated['recurring_rules'] = $this->buildRecurringRules($request);
        }

        try {
            DB::beginTransaction();
            $holiday = holidayCalendar::create($validated);

            Log::info('HOLIDAY CREATED', ['id' => $holiday->id, 'name' => $holiday->name]);

            DB::commit();

            return redirect()->route('holiday-calendars.index')->with('success', 'Holiday successfully added');
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
    public function show(holidayCalendar $holidayCalendar)
    {
        // return view('holiday_calendars.show', compact('holidayCalendar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(holidayCalendar $holidayCalendar)
    {
        return view('holiday_calendars.edit', compact('holidayCalendar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, holidayCalendar $holidayCalendar)
    {
        Log::info('UPDATE ROUTE REACHED', $request->all());
        Log::info('VALIDATION START');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:' . implode(',', array_column(HolidayType::cases(), 'value')),
            'is_recurring' => 'boolean',
            'recurring_pattern' => 'required_if:is_recurring,1|in:' . implode(',', array_column(RecurringPattern::cases(), 'value')),
            'recurring_rules' => 'nullable|json',
            'description' => 'nullable|string'
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        Log::info('VALIDATION PASSED');
        $validated = $validator->validated();
        if ($request->is_recurring && $request->recurring_pattern !== 'none') {
            $validated['recurring_rules'] = $this->buildRecurringRules($request);
        } else {
            $validated['recurring_rules'] = null;
        }

        try {
            DB::beginTransaction();
            $holidayCalendar->update($validated);

            Log::info('HOLIDAY UPDATED', ['id' => $holidayCalendar->id, 'name' => $holidayCalendar->name]);

            DB::commit();
            return redirect()->route('holiday-calendars.index')->with('success', 'Holiday Calendar successfully updated');
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
    public function destroy(holidayCalendar $holidayCalendar)
    {
        try {
            DB::beginTransaction();
            $holidayCalendar->delete();
            DB::commit();
            return back()->with('success', 'Holiday Calendar successfully deleted');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()));
        }
    }

    public function events(Request $request)
    {
        Log::info('Fetching all holidays');

        $holidays = HolidayCalendar::all();
        $instances = [];

        foreach ($holidays as $holiday) {
            if ($holiday->is_recurring && $holiday->recurring_pattern !== 'none') {

                $instances = array_merge(
                    $instances,
                    $holiday->generateRecurringInstances(
                        now()->startOfYear(),
                        now()->endOfYear()
                    )
                );
            } else {
                $instances[] = array_merge($holiday->toArray(), [
                    'is_recurring_instance' => false,
                ]);
            }
        }

        return response()->json(collect($instances)->map(function ($holiday) {
            return [
                'id' => $holiday['id'],
                'title' => $holiday['name'],
                'start' => $holiday['date'],
                'allDay' => true,
                'extendedProps' => [
                    'type' => $holiday['type'],
                    'description' => $holiday['description'],
                    'is_recurring' => $holiday['is_recurring_instance'] ?? false,
                ],
                'backgroundColor' => $this->getEventColor($holiday['type']),
                'borderColor' => $this->getEventColor($holiday['type']),
            ];
        }));
    }

    private function getEventColor($type)
    {
        return match ($type) {
            'Public Holiday' => '#4DB6AC',
            'School Holiday' => '#FF8A65',
            'Religious Holiday' => '#9575CD',
            'National Holiday' => '#81C784',
            'Other Holiday' => '#FFD54F',
            default => '#64B5F6',
        };
    }

    private function buildRecurringRules(Request $request)
    {
        $date = Carbon::parse($request->date);
        $pattern = $request->recurring_pattern;

        switch ($pattern) {
            case 'yearly':
                return [
                    'month' => $date->month,
                    'day' => $date->day,
                ];

            case 'floating':

                $weekOfMonth = ceil($date->day / 7);
                return [
                    'month' => $date->month,
                    'weekday' => $date->format('l'),
                    'week' => $weekOfMonth,
                ];

            case 'easter_based':
                $year = $date->year;
                $easter = $this->calculateEaster($year);
                $daysDiff = $date->diffInDays($easter, false);
                return [
                    'days_offset' => $daysDiff,
                ];

            default:
                return null;
        }
    }

    private function calculateEaster($year)
    {
        $easter = new \DateTime("$year-03-21");
        $easter->modify('+' . easter_days($year) . ' days');

        return Carbon::instance($easter);
    }
}
