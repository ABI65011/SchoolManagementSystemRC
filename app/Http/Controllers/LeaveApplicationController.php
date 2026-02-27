<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\LeaveAction;
use App\Helpers\LeaveStatus;
use App\Helpers\LeaveType;
use App\Helpers\ReplacementStatus;
use App\Models\leave_application;
use App\Models\leave_approval;
use App\Models\staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeaveApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $user = Auth::user();
        $viewAll = $request->query('view') === 'all';

        if ($user->hasAnyRole(['Admin', 'Super'])) {

            $allApplications = leave_application::with(['employee.user', 'supervisor.user', 'approvals', 'replacement.user'])
                ->latest()->get();
        } else {

            if ($viewAll && (
                $user->hasAnyRole(['HR|Staff'])
            )) {
                $allApplications = leave_application::with('employee.user', 'supervisor.user', 'approvals')
                    ->latest()
                    ->get();
            } else {
                $allApplications = leave_application::with('employee.user', 'supervisor.user', 'approvals')
                    ->where(function ($q) use ($user) {
                        $q->where('employee_id', $user->staff?->id)
                            ->orWhereHas('approvals', function ($q2) use ($user) {
                                $q2->where('approver_id', $user->id)->whereNull('signed_at');
                            })->orWhere(function ($q3) use ($user) {
                                $q3->where('replacement_employee_id', $user->staff?->id)
                                    ->where('replacement_status', ReplacementStatus::Pending->value);
                            });
                    })
                    ->latest()->get();
            }
        }
        // dd($user->staff?->id);

        return view('leave_applications.index', compact('allApplications', 'viewAll'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = staff::with('user')->get();
        $supervisors = staff::with('user')->whereHas('user.roles', function ($q) {
            $q->where('name', 'HR');
        })->get();
        $leaveTypes = LeaveType::cases();
        $loggedInStaff = Auth::user()->staff()->with('supervisor.staff')->first();



        return view('leave_applications.create', compact('employees', 'supervisors', 'leaveTypes', 'loggedInStaff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id'   => 'required|exists:staff,id',
            'supervisor_id' => 'nullable|exists:staff,id',
            'replacement_employee_id' => [
                'required',
                'exists:staff,id',
                function ($attr, $value, $fail) use ($request) {
                    if ($value == $request->employee_id) {
                        $fail('Replacement cannot be yourself.');
                    }
                }
            ],
            'type'          => 'required|string',
            'other_reason'  => 'nullable|string',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'study_days_note' => 'nullable|string',
        ]);

        $validated = $validator->validated();

        $validated['status'] = LeaveStatus::Awaiting_Replacement_Confirmation->value;
        $validated['replacement_status'] = ReplacementStatus::Pending->value;
        try {
            DB::beginTransaction();

            $application = leave_application::create($validated);
            Log::info('Leave application created', ['id' => $application->id, 'employee_id' => $application->employee_id]);

            DB::commit();
            return redirect()->route('leave.applications.index')->with('success', 'Leave application successfully created!');
        } catch (\Throwable $th) {
            Log::error('EXCEPTION INSIDE TRY', [
                'msg' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            DB::rollBack();
            return back()->with('error', AppHelper::buildExceptionMessage($th->getMessage()))->withInput();
        }
    }


    public function replacementRespond(Request $request, leave_application $application)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
        ]);


        if (!Auth::user()->staff || Auth::user()->staff->id !== $application->replacement_employee_id) {
            abort(403, 'You are not the assigned replacement.');
        }

        if ($request->action === 'accept') {
            $application->update([
                'replacement_status' => ReplacementStatus::Accepted->value,
                'status' => LeaveStatus::Pending->value,
            ]);


            // leave_approval::firstOrCreate([
            //     'leave_application_id' => $application->id,
            //     'approver_id' => $application->supervisor_id,
            //     'stage' => 1,
            // ], [
            //     'approver_role' => 'Supervisor',
            //     'action' => LeaveAction::Pending->value,
            // ]);

            $hrStaff = staff::whereHas('user.roles', function ($q) {
                $q->where('name', 'HR');
            })->first();

            if (!$hrStaff) {
                return redirect()->route('leave.applications.index')
                    ->with('error', 'No HR staff found to process this application. Please contact admin.');
            }

            leave_approval::firstOrCreate(
                [
                    'leave_application_id' => $application->id,
                    'stage' => 1,
                ],
                [
                    'approver_role' => 'HR',
                    'approver_id' => $hrStaff->user->id,
                    'action' => LeaveAction::Pending->value,
                ]
            );

            return redirect()->route('leave.applications.index')->with('success', 'You accepted to take over duties. Approval process started.');
        }

        $application->update([
            'replacement_status' => ReplacementStatus::Rejected->value,
            'status' => LeaveStatus::Replacement_Rejected->value,
        ]);


        return redirect()->route('leave.applications.index')->with('error', 'You rejected the replacement request.');
    }




    /**
     * Display the specified resource.
     */
    public function show(leave_application $leave_application)
    {
        $leave_application->load([
            'employee.user',
            'supervisor.user',
            'replacement.user',
            'approvals.approver'
        ]);
        // dd($leave_application);
        return view('leave_applications.show', compact('leave_application'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(leave_application $leave_application)
    {

        $user = Auth::user();
        $isApplicant = $leave_application->employee_id  == $user->staff?->id;
        $canEdit = false;

        if ($user->hasAnyRole(['Admin', 'Super'])) {
            $canEdit = true;
        } elseif ($isApplicant && $leave_application->replacement_status === ReplacementStatus::Rejected->value) {
            $canEdit = true;
        }

        if (!$canEdit) {
            return redirect()->route('leave.application.show', $leave_application->id)
                ->with('error', 'You cannot edit this application at this stage.');
        }

        $employees = staff::with('user')->get();
        $supervisors = staff::with('user')->get();
        $leaveTypes = LeaveType::cases();
        $replacements = staff::with('user')->where('id', '!=', $leave_application->employee_id)->get();

        return view('leave_applications.edit', compact(
            'leave_application',
            'employees',
            'supervisors',
            'leaveTypes',
            'replacements',
            'isApplicant'
        ));
    }

    /**a
     * Update the specified resource in storage.
     */
    public function update(Request $request, Leave_application $leave_application)
    {
        $request->validate([
            'employee_id'   => 'required|exists:staff,id',
            'supervisor_id' => 'nullable|exists:staff,id',
            'replacement_employee_id' => 'nullable|exists:staff,id',
            'type'          => 'required|string',
            'other_reason'  => 'nullable|string',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'study_days_note' => 'nullable|string',
        ]);

        $user = Auth::user();
        $isApplicant = $leave_application->employee_id == $user->staff->id;

        $oldReplacement = $leave_application->replacement_employee_id;
        $newReplacement = $request->replacement_employee_id;

        if ($oldReplacement != $newReplacement) {
            $request->merge([
                'replacement_status' => ReplacementStatus::Pending->value,
            ]);
        }

        $leave_application->update($request->all());

        return redirect()->route('leave.applications.index')
            ->with('success', 'Leave application updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(leave_application $leave_application)
    {
        $leave_application->delete();

        return redirect()->route('leave.applications.index')
            ->with('success', 'Leave application deleted!');
    }
}
