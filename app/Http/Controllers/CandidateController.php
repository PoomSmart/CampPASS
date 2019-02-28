<?php

namespace App\Http\Controllers;

use App\Common;
use App\Candidate;
use App\FormScore;
use App\QuestionSet;

use App\Http\Controllers\QualificationController;

use App\Enums\ApplicationStatus;

use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public static function rank(QuestionSet $question_set, bool $list = false)
    {
        $form_scores = FormScore::where('question_set_id', $question_set->id);
        if (!$form_scores->exists())
            throw new \CampPASSExceptionRedirectBack('No application forms to be ranked.');
        $form_scores = $form_scores->with('registration')->whereHas('registration', function ($query) {
             // These unsubmitted forms by common sense should be rejected from the grading process at all
            $query->where('status', ApplicationStatus::APPLIED);
        });
        $total_registrations = $form_scores->count();
        if ($question_set->manual_required)
            $form_scores = $form_scores->where('finalized', true); // We would not grade unfinalized answers
        else {
            // If the question set can be entirely automatically graded, we say it is finalized
            $form_scores->update([
                'finalized' => true,
            ]);
        }
        if ($question_set->announced) {
            $minimum_score = $question_set->total_score * $question_set->score_threshold;
            $form_scores = $form_scores->where(function ($query) use (&$question_set, &$minimum_score) {
                $query->where('total_score', '>='. $minimum_score);
            });
        }
        if (!$form_scores->exists())
            throw new \CampPASSExceptionRedirectBack('No finalized application forms to be ranked.');
        if (!$question_set->announced && $form_scores->count() !== $total_registrations)
            throw new \CampPASSExceptionRedirectBack('All application forms must be finalized before ranking.');
        foreach ($form_scores->get() as $form_score) {
            if (is_null($form_score->total_score)) {
                $form_score->update([
                    'total_score' => QualificationController::answer_grade($registration_id = $form_score->registration_id, $question_set_id = $question_set->id, $silent = true),
                ]);
            }
        }
        $form_scores = $form_scores->orderByDesc('total_score');
        if ($list)
            return $form_scores->get();
        $camp = $question_set->camp;
        $max = config('const.app.max_paginate');
        $form_scores = $form_scores->paginate($max);
        return view('qualification.candidate_rank', compact('form_scores', 'question_set', 'camp'))->with('i', (request()->input('page', 1) - 1) * $max);
    }

    public static function announce(QuestionSet $question_set, bool $void = false)
    {
        if ($question_set->announced)
            throw new \CampPASSExceptionRedirectBack('Candidates for this camp are already announced.');
        // The qualified campers are those that have form score passing the criteria
        $no_passed = 0;
        $form_scores = self::rank($question_set, $list = true);
        $form_scores->each(function ($form_score) use (&$question_set) {
            if ($form_score->total_score / $question_set->total_score >= $question_set->score_threshold)
                ++$no_passed;
        });
        if (!$no_passed)
            throw new \CampPASSExceptionRedirectBack('There are no campers to announce to.');
        $candidates = [];
        $camp_procedure = $question_set->camp->camp_procedure;
        foreach ($form_scores as $form_score) {
            $registration = $form_score->registration;
            $passed = $form_score->total_score / $question_set->total_score >= $question_set->score_threshold;
            if ($passed) {
                // The application form can be approved now if they do not need to pay the deposit
                $registration->update([
                    'status' => $camp_procedure->deposit_required ? ApplicationStatus::CHOSEN : ApplicationStatus::APPROVED,
                ]);
            } else {
                // Otherwise, the application form will be rejected
                $registration->update([
                    'status' => ApplicationStatus::REJECTED,
                ]);
            }
            $candidates[] = [
                'registration_id' => $form_score->registration_id,
                'total_score' => $form_score->total_score,
            ];
        }
        Candidate::insert($candidates);
        // TODO: somehow notify these candidates
        // Candidates are finalized, this question set will no longer be editable
        $question_set->update([
            'announced' => true,
        ]);
        if (!$void)
            return redirect()->back()->with('success', 'Candidates are announced.');
    }
}
