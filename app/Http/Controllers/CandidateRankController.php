<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Common;
use App\QuestionSet;

use App\Enums\QuestionType;

use Illuminate\Http\Request;

class CandidateRankController extends Controller
{
    public function rank(QuestionSet $question_set)
    {
        $answers = Answer::where('question_set_id', $question_set->id)->get();
        if (empty($answers))
            return redirect('/')->with('error', 'You cannot rank the application form without questions.');
        $camp = $question_set->camp();
        $json = Common::getQuestionJSON($camp->id, $graded = true);
        $scores = [];
        foreach ($answers as $answer) {
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
        return view('qualification.candidate_rank', compact('scores'));
    }
}
