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
        $has_question_set = !is_null($question_set);
        $has_interview = $camp->camp_procedure->interview_required;
        $by_time = $has_question_set && !$question_set->total_score;
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
            $education_level = $educations[$camper->education_level];
            if (!isset($education_freq[$education_level]))
                $education_freq[$education_level] = 0;
            ++$education_freq[$education_level];
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
                $is_passed = false;
                if ($has_question_set) {
                    $form_score = $registration->form_score;
                    $is_passed = $form_score->passed;
                    $average_score += $form_score->total_score;
                    ++$score_count;
                } else
                    $is_passed = $registration->chosen_to_confirmed();
                if ($is_passed && $has_interview)
                    $is_passed = $registration->interviewed_to_confirmed();
                if ($is_passed)
                    ++$passed;
                else
                    ++$rejected;
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
        // TODO: Gender legend manual sorting is currently possible using Google Chart unless using hacky workaround
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
        // TODO: Education level legend manual sorting is currently possible using Google Chart unless using hacky workaround
        $localized_educations = EducationLevel::getLocalizedConstants('year');
        foreach ($education_freq as $education_level => $total) {
            $education_table->addRow([
                $localized_educations[$education_level]->name, $total,
            ]);
        }
        $education_chart = \Lava::PieChart('Educations', $education_table, [

        ]);
        // Provinces
        arsort($province_freq);
        $top_province_freq = array_slice($province_freq, 0, 5, true);
        $data['top_provinces'] = array_map(function ($province_id, $freq) {
            return [
                'province' => Province::find($province_id),
                'freq' => $freq,
            ];
        }, array_keys($top_province_freq), $top_province_freq);
        // Schools
        arsort($school_freq);
        $top_school_freq = array_slice($school_freq, 0, 5, true);
        $data['top_schools'] = array_map(function ($school_id, $freq) {
            return [
                'school' => School::find($school_id),
                'freq' => $freq,
            ];
        }, array_keys($top_school_freq), $top_school_freq);
        return view('analytic.analytic', compact('camp', 'data'));
    }
}
