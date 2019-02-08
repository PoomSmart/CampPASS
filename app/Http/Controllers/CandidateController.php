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
    public function rank(QuestionSet $question_set, $list = false)
    {
        $form_scores = FormScore::where('question_set_id', $question_set->id);
        if (!$form_scores->exists()) {
            if ($list)
                return null;
            throw new \App\Exceptions\CampPASSException('You cannot rank the application form without questions.');
        }
        $form_scores = $form_scores->get()->filter(function ($form_score) {
             // We would not grade unsubmitted forms
            return $form_score->registration()->applied();
        });
        $total_registrations = $form_scores->count();
        $form_scores = $form_scores->filter(function ($form_score) use ($question_set) {
            // We would not grade unfinalized answers
            return $form_score->finalized && ($question_set->announced ? ($form_score->total_score / $question_set->total_score >= $question_set->score_threshold) : true);
        });
        if ($form_scores->isEmpty()) {
            if ($list)
                return null;
            throw new \App\Exceptions\CampPASSExceptionRedirectBack('No finalized application forms to rank.');
        }
        if (!$question_set->announced && $form_scores->count() !== $total_registrations) {
            if ($list)
                return null;
            throw new \App\Exceptions\CampPASSExceptionRedirectBack('All application forms must be finalized before ranking.');
        }
        foreach ($form_scores as $form_score) {
            if (is_null($form_score->total_score)) {
                $registration = $form_score->registration();
                $question_set = $form_score->question_set();
                $form_score->total_score = QualificationController::answer_grade($registration->id, $question_set->id, $silent = true);
                $form_score->save();
            }
        }
        $form_scores = $form_scores->sortByDesc(function ($form_score) {
            return $form_score->total_score;
        });
        if ($list)
            return $form_scores;
        // $max = config('const.app.max_paginate');
        // TODO: pagination ?
        return view('qualification.candidate_rank', compact('form_scores', 'question_set'))/*->with('i', ($request->input('page', 1) - 1) * $max)*/;
    }

    public function announce(QuestionSet $question_set)
    {
        if ($question_set->announced)
            throw new \App\Exceptions\CampPASSExceptionRedirectBack('Candidates for this camp are already announced.');
        $form_scores = $this->rank($question_set, $list = true)->filter(function ($form_score) use ($question_set) {
            return $form_score->total_score / $question_set->total_score >= $question_set->score_threshold;
        });
        if (!$form_scores)
            throw new \App\Exceptions\CampPASSExceptionRedirectBack('There are no campers to announce to.');
        $candidates = [];
        foreach ($form_scores as $form_score) {
            $candidates[] = [
                'registration_id' => $form_score->registration_id,
                'total_score' => $form_score->total_score,
            ];
        }
        Candidate::insert($candidates);
        // TODO: somehow notify these candidates
        // Candidates are finalized, this question set will no longer be editable
        $question_set->announced = true;
        $question_set->save();
        return redirect()->back()->with('success', 'Candidates are announced.');
    }
}
