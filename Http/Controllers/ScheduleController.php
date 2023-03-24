<?php

namespace Modules\Schedule\Http\Controllers;

use App\Models\Day;
use App\Models\Branch;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Academics\Entities\SClass;
use App\Models\EmployeeSubjectSchedule;
use Modules\Academics\Entities\Section;
use Modules\Academics\Entities\Session;
use Illuminate\Contracts\Support\Renderable;
use Modules\Academics\Entities\AssignSubject;
use Modules\Schedule\Entities\TimingSchedule;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {

        $sessions = Session::get();
        $branches = Branch::get();
        if (count($request->all()) > 0) {
            // return $request->all();
            return redirect()->route('schedule-filter-action', [$request->action, $request->session, $request->branch, $request->class, $request->section]);
        }
        return view('academic.schedule', compact(['branches', 'sessions']));
    }


    public function actionFilter($action, $session, $branch, $class, $section)
    {


        // return $branch;with(['sections'])->
        $sessions = Session::get();
        $branches = Branch::get();
        $branche = Branch::find($branch);
        $session = Session::find($session);
        $section = Section::find($section);
        $dsections = Section::whereHas('branches', function ($query) use ($branche) {
            $query->where('branch_id', $branche->id);
        })->get();


        $dclass = SClass::whereHas('branches', function ($query) use ($branche) {
            $query->where('branch_id', $branche->id);
        })->get();


        $class = SClass::find($class);
        $subjects = AssignSubject::with(['subject'])->where('branch_id', $branche->id)
            ->where('s_class_id', $class->id)
            ->where('section_id', $section->id)
            ->get();
        // return $subjects;
        $schedules = TimingSchedule::where('branch_id', $branche->id)->get();
        // return $schedules;
        $employees = Employee::with(['user'])->where(['branch_id' => $branche->id])->get();


        // return $employees;
        $assignments = EmployeeSubjectSchedule::with(['schedule', 'branch', 'section', 'class', 'day', 'employee', 'subject'])
            ->where('branch_id', $branche->id)
            ->where('s_class_id', $class->id)
            ->where('section_id', $section->id)

            ->get();
        $days = Day::get();
        // return $subjects;
        // return $assignments;//,,,,,,,,,,,,,,,,,
        return view('academic.schedule', compact([
            'branches',
            'action',
            'sessions',
            'section',
            'class',
            'branche',
            'session',
            'schedules',
            'days',
            'subjects',
            'employees',
            'assignments',
            'dsections',
            'dclass'

        ]));

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        // return $request;
        $subjects = $request->subject;
        $teacher = $request->teacher;
        $previous = EmployeeSubjectSchedule::where(
            [
                'branch_id' => $request->branch_id,
                'section_id' => $request->section_id,
                's_class_id' => $request->class_id,
            ]
        )->delete();
        // $previous->delete();
        // return $previous;

        foreach ($subjects as $info => $subject) {
            // echo $info ."<br>";
            $param = json_decode($info, TRUE);
            $subject_id = $subject;
            $day_id = $param['day'];
            $schedule_id = $param['schedule'];
            $employee_id = $teacher[$info];

            EmployeeSubjectSchedule::updateOrCreate([
                'employee_id' => $employee_id,
                'day_id' => $day_id,
                'subject_id' => $subject_id,
                'timing_schedule_id' => $schedule_id,
                'branch_id' => $request->branch_id,
                'section_id' => $request->section_id,
                's_class_id' => $request->class_id,
            ]);





            // echo $employee."<br>";


            // echo "<br>Subject = ". $subject."<br> Day = ".  ."<br> Schedule = ". . "<br>";
            // foreach(json_decode($info) as $parma ){
            //     echo $;
            // }
        }

        return redirect()->route('schedule-filter-action', ["result", $request->session, $request->branch_id, $request->class_id, $request->section_id]);

        return redirect()->back()->with('success', 'Teacher successully assign subject');
        // return count($subjects);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($session, $branch, $class, $section)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}