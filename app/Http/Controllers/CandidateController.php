<?php

namespace App\Http\Controllers;

use App\Common;
use App\FormScore;
use App\QuestionSet;

use App\Http\Controllers\QualificationController;

use App\Enums\RegistrationStatus;

use Illuminate\Http\Request;

class CandidateController extends Controller
{
    protected $scores;

    public function score_rank($a, $b)
    {
        $score_a = $this->scores[$a->id];
        $score_b = $this->scores[$b->id];
        return $score_a == $score_b ? 0 : $score_a < $score_b ? 1 : -1;
    }

    public function rank(QuestionSet $question_set)
    {
        $form_scores = FormScore::where('question_set_id', $question_set->id);
        if (!$form_scores->exists())
            throw new \App\Exceptions\CampPASSException('You cannot rank the application form without questions.');
        $form_scores = $form_scores->get()->filter(function ($form_score) {
             // We would not grade unsubmitted forms
            return $form_score->registration()->applied();
        });
        $total_registrations = $form_scores->count();
        $form_scores = $form_scores->filter(function ($form_score) {
            // We would not grade unfinalized answers
            return $form_score->finalized;
        });
        if ($form_scores->isEmpty())
            throw new \App\Exceptions\CampPASSExceptionRedirectBack('No finalized application forms to rank.');
        if ($form_scores->count() !== $total_registrations)
            throw new \App\Exceptions\CampPASSExceptionRedirectBack('All application forms must be finalized before ranking.');
        $camp = $question_set->camp();
        $json = Common::getQuestionJSON($camp->id, $graded = true);
        $scores = [];
        foreach ($form_scores as $form_score) {
            $registration = $form_score->registration();
            $question_set = $form_score->question_set();
            if (is_null($form_score->total_score)) {
                $form_score->total_score = QualificationController::answer_grade($registration->id, $question_set->id, $silent = true);
                $form_score->save();
            }
            $camper = $registration->camper();
            $scores[$camper->id] = $form_score->total_score;
        }
        $this->scores = $scores;
        // $max = config('const.app.max_paginate');
        $campers = $camp->campers($status = RegistrationStatus::APPLIED)->all();
        usort($campers, [get_class(), 'score_rank']);
        // TODO: pagination
        return view('qualification.candidate_rank', compact('campers', 'scores', 'question_set'))/*->with('i', ($request->input('page', 1) - 1) * $max)*/;
    }
}