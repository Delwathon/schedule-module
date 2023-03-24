<?php

namespace Modules\Schedule\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Schedule\Entities\TimingSchedule;

class TimingController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {

        $branches = Branch::get();
        // return $request->all();
        if ($request->branch_id) {
            return redirect()->route('timing.show', $request->branch_id);
        }

        return view('academic.timing', compact(['branches']));
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



    public function search($id)
    {


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch' => 'required|numeric',
            'schedule_row' => 'required|array',
            'schedule_row.*.start' => 'required|string',
            'schedule_row.*.end' => 'required|string',
            'schedule_row.*.title' => 'required|string'
        ]);
        // return $request->schedule_row;

        $timings = TimingSchedule::where('branch_id', $request->branch)->get();
        $remainingDbTimingIds = [];

        if (count($timings) > 0) {
            foreach ($timings as $timing) {
                $remainingDbTimingIds[] = $timing->id;
            }
        }

        foreach ($request->schedule_row as $schedule) {
            // return $schedule['schedule_id'];
            if (in_array($schedule['schedule_id'], $remainingDbTimingIds)) {
                TimingSchedule::where('id', $schedule['schedule_id'])->update([
                    'title' => $schedule['title'],
                    'end' => $schedule['end'],
                    'start' => $schedule['start'],
                    'branch_id' => $request->branch
                ]);
                $array_difference = array_diff($remainingDbTimingIds, array($schedule['schedule_id']));
                $remainingDbTimingIds = array_values($array_difference);
            } else {
                TimingSchedule::create([
                    'title' => $schedule['title'],
                    'end' => $schedule['end'],
                    'start' => $schedule['start'],
                    'branch_id' => $request->branch
                ]);
            }
        }

        if (count($remainingDbTimingIds) > 0) {
            for ($i = 0; $i < count($remainingDbTimingIds); $i++) {
                TimingSchedule::where('id', $remainingDbTimingIds[$i])->delete();
            }
        }

        return redirect()->back()->with('success', 'Timing Schedule added successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $branches = Branch::get();
        $schedules = TimingSchedule::with(['branch'])->where('branch_id', $id)->get();

        $branche = Branch::find($id);


        return view('academic.timing', compact(['branches', 'branche', 'schedules']));
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