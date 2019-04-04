<?php

namespace App\Http\Controllers;

use App\Camp;

use Carbon\Carbon;

use Illuminate\Http\Request;

class AnalyticController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:camper-list');
    }

    public function analytic(Camp $camp)
    {
        $table = \Lava::DataTable();
        $table->addDateColumn('app.Date')->addNumberColumn('registration.Applicants');
        $freq = [];
        $carbon = [];
        foreach ($camp->registrations()->orderBy('submission_time')->get() as $registration) {
            $submission_time = $registration->submission_time;
            $day = $submission_time->day;
            if (!isset($freq[$day])) {
                $freq[$day] = 1;
                $carbon[$day] = $submission_time;
            }
            ++$freq[$day];
        }
        foreach ($freq as $day => $total) {
            $table->addRow([
                $carbon[$day]->toDateString(), $total,
            ]);
        }
        $chart = \Lava::LineChart('Applicants', $table);
        return view('analytic.analytic', compact('camp'));
    }
}
