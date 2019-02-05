<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Common;
use App\QuestionSet;
use App\User;

use App\Enums\QuestionType;
use App\Enums\RegistrationStatus;

use Illuminate\Http\Request;

class CandidateRankController extends Controller
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
        $answers = Answer::where('question_set_id', $question_set->id);
        if (!$answers->exists())
            throw new \App\Exceptions\CampPASSException('You cannot rank the application form without questions.');
        $answers = $answers->get();
        $camp = $question_set->camp();
        $json = Common::getQuestionJSON($camp->id, $graded = true);
        $scores = [];
        foreach ($answers as $answer) {
            // We would not grade unsubmitted answers
            if ($answer->registration()->unsubmitted()) continue;
            $question = $answer->question();
            $camper = $answer->camper();
            $answer = $answer->answer;
            if (!isset($scores[$camper->id]))
                $scores[$camper->id] = 0;
            $json_id = $question->json_id;
            if (isset($json['question_graded'][$json_id])) {
                if ($question->type == QuestionType::CHOICES) {
                    $solution = $json['radio'][$json_id];
                    $score = $solution == $answer ? $question->full_score : 0;
                    $scores[$camper->id] += $score;
                }
            }
        }
        $this->scores = $scores;
        //$max = config('const.app.max_paginate');
        $campers = $camp->campers($status = RegistrationStatus::APPLIED)->all();
        usort($campers, [get_class(), 'score_rank']);
        // TODO: pagination
        return view('qualification.candidate_rank', compact('campers', 'scores'))/*->with('i', ($request->input('page', 1) - 1) * $max)*/;
    }
}
