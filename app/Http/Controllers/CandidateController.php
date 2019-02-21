<?php

namespace App\Http\Controllers;

use App\Common;
use App\Candidate;
use App\FormScore;
use App\QuestionSet;

use App\Http\Controllers\QualificationController;

use App\Enums\RegistrationStatus;

use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public static function rank(QuestionSet $question_set, bool $list = false)
    {
        if ($question_set->announced)
            throw new \CampPASSException('The candidates have already been announced.');
        $form_scores = FormScore::where('question_set_id', $question_set->id);
        if (!$form_scores->exists())
            throw new \CampPASSException('You cannot rank the application form without questions.');
        $form_scores = $form_scores->get()->filter(function ($form_score) {
             // These unsubmitted forms by common sense should be rejected from the grading process at all
            $applied = $form_score->registration()->applied();
            // TODO: Must also check if this registration has the associated approved payment slip
            return $applied;
        });
        $total_registrations = $form_scores->count();
        $form_scores = $form_scores->filter(function ($form_score) use (&$question_set) {
            // We would not grade unfinalized answers
            return $form_score->finalized && ($question_set->announced ? ($form_score->total_score / $question_set->total_score >= $question_set->score_threshold) : true);
        });
        if ($form_scores->isEmpty())
            throw new \CampPASSExceptionRedirectBack('No finalized application forms to rank.');
        if (!$question_set->announced && $form_scores->count() !== $total_registrations)
            throw new \CampPASSExceptionRedirectBack('All application forms must be finalized before ranking.');
        foreach ($form_scores as $form_score) {
            if (is_null($form_score->total_score)) {
                $registration = $form_score->registration();
                $question_set = $form_score->question_set();
                $form_score->update([
                    'total_score' => QualificationController::answer_grade($registration->id, $question_set->id, $silent = true),
                ]);
            }
        }
        $form_scores = $form_scores->sortByDesc(function ($form_score) {
            return $form_score->total_score;
        });
        if ($list)
            return $form_scores;
        $camp = $question_set->camp();
        // $max = config('const.app.max_paginate');
        // TODO: pagination ?
        return view('qualification.candidate_rank', compact('form_scores', 'question_set', 'camp'))/*->with('i', ($request->input('page', 1) - 1) * $max)*/;
    }

    public static function announce(QuestionSet $question_set, bool $void = false)
    {
        if ($question_set->announced)
            throw new \CampPASSExceptionRedirectBack('Candidates for this camp are already announced.');
        // The qualified campers are those that have form score passing the criteria
        $form_scores = self::rank($question_set, $list = true)->filter(function ($form_score) use (&$question_set) {
            return $form_score->total_score / $question_set->total_score >= $question_set->score_threshold;
        });
        if ($form_scores->isEmpty())
            throw new \CampPASSExceptionRedirectBack('There are no campers to announce to.');
        $candidates = [];
        foreach ($form_scores as $form_score) {
            $registration = $form_score->registration();
            $registration->update([
                'status' => RegistrationStatus::QUALIFIED,
            ]);
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
