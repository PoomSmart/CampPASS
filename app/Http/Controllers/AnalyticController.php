<?php

namespace App\Http\Controllers;

use App\Camp;
use App\Common;
use App\Province;
use App\School;
use App\User;

use App\Enums\EducationLevel;

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
        $total_registration_freq = []; $gender_freq = []; $education_freq = []; $province_freq = []; $school_freq = [];
        $passed = $withdrawed = $rejected = $score_count = $peak_date_count = 0;
        $educations = User::$education_level_to_year;
        $data = [];
        $registration_table = \Lava::DataTable();
        $registration_table->addDateColumn(trans('analytic.Date'))->addNumberColumn(trans('analytic.Applicants'));
        $registrations = $camp->registrations()->orderBy('submission_time')->get();
        $data['total'] = $registrations->count();
        $average_score = 0.0;
        $question_set = $camp->question_set;
        $has_passed = !is_null($question_set);
        $by_time = $has_passed && !$question_set->total_score;
        $peak_date = null;
        foreach ($registrations as $registration) {
            $submission_time = $registration->submission_time;
            $slot = $submission_time->toDateString();
            if (!isset($total_registration_freq[$slot]))
                $total_registration_freq[$slot] = 0;
            $camper = $registration->camper;
            $gender = $camper->gender;
            if (!isset($gender_freq[$gender]))
                $gender_freq[$gender] = 0;
            ++$gender_freq[$gender];
            $year = $educations[$camper->education_level];
            if (!isset($education_freq[$year]))
                $education_freq[$year] = 0;
            ++$education_freq[$year];
            if (!isset($province_freq[$camper->province_id]))
                $province_freq[$camper->province_id] = 0;
            ++$province_freq[$camper->province_id];
            if (!isset($school_freq[$camper->school_id]))
                $school_freq[$camper->school_id] = 0;
            ++$school_freq[$camper->school_id];
            ++$total_registration_freq[$slot];
            if ($registration->withdrawed())
                ++$withdrawed;
            else if ($registration->rejected())
                ++$rejected;
            else {
                if ($has_passed) {
                    $form_score = $registration->form_score;
                    if ($form_score->passed)
                        ++$passed;
                    $average_score += $form_score->total_score;
                    ++$score_count;
                } else {
                    if ($registration->chosen_to_confirmed())
                        ++$passed;
                }
            }
        }
        $data['passed'] = $passed;
        $data['withdrawed'] = $withdrawed;
        $data['rejected'] = $rejected;
        $data['average_score'] = !$by_time && $score_count ? number_format($average_score / $score_count, 2).' / '.$question_set->total_score : '-';
        foreach ($total_registration_freq as $slot => $total) {
            $registration_table->addRow([
                $slot, $total,
            ]);
            if ($total > $peak_date_count) {
                $peak_date_count = $total;
                $peak_date = $slot;
            }
        }
        $data['peak_date'] = Common::formattedDate($peak_date);
        $registration_chart = \Lava::LineChart('Applicants', $registration_table, [
            'title' => trans('analytic.ApplicantsPerDay'),
            'legend' => [
                'position' => 'none',
            ],
            'hAxis' => [
                'title' => trans('analytic.Date'),
                'format' => 'MMM d',
                'minorGridlines' => [
                    'count' => 0,
                ],
            ],
            'vAxis' => [
                'title' => trans('analytic.Applicants'),
                'format' => '#',
                'baseline' => 0,
                'minorGridlines' => [
                    'count' => 0,
                ],
            ],
        ]);
        // Genders
        $gender_table = \Lava::DataTable();
        $gender_table->addStringColumn(trans('account.Gender'))->addNumberColumn('GenderCount');
        $genders = [
            trans('account.Male'),
            trans('account.Female'),
            trans('account.OtherGender'),
        ];
        foreach ($gender_freq as $gender => $total) {
            $gender_table->addRow([
                $genders[$gender], $total,
            ]);
        }
        $gender_chart = \Lava::PieChart('Genders', $gender_table, [

        ]);
        // Education levels
        $education_table = \Lava::DataTable();
        $education_table->addStringColumn(trans('account.EducationLevel'))->addNumberColumn('EducationLevelCount');
        $localized_educations = EducationLevel::getLocalizedConstants('year');
        foreach ($education_freq as $year => $total) {
            $education_table->addRow([
                $localized_educations[$year]->name, $total,
            ]);
        }
        $education_chart = \Lava::PieChart('Educations', $education_table, [

        ]);
        // Provinces
        arsort($province_freq);
        $top_province_freq = array_slice($province_freq, 0, 5, true);
        $data['top_provinces'] = array_map(function ($province_id) {
            return Province::find($province_id);
        }, array_keys($top_province_freq));
        // Schools
        arsort($school_freq);
        $top_school_freq = array_slice($school_freq, 0, 5, true);
        $data['top_schools'] = array_map(function ($school_id) {
            return School::find($school_id);
        }, array_keys($top_school_freq));
        return view('analytic.analytic', compact('camp', 'data'));
    }
}
