<?php

namespace App\Http\Controllers;

use App\Common;
use App\Camp;
use App\QuestionSet;
use App\QuestionManager;

use App\Http\Requests\StoreQuestionRequest;

use App\Enums\QuestionType;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class QuestionSetController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:question-list');
        $this->middleware('permission:question-create', ['only' => ['show', 'store']]);
        $this->middleware('permission:question-edit', ['only' => ['finalize', 'question_set_auto_ranked']]);
    }

    public function store(StoreQuestionRequest $request, Camp $camp)
    {
        Common::authenticate_camp($camp);
        $content = $request->all();
        $question_set = QuestionManager::createOrUpdateQuestionSet($camp, $content, $request->input('minimum_score'));
        $this->question_set_auto_ranked($question_set);
        if (!$question_set->finalized)
            return redirect()->back()->with('success', trans('question.QuestionsSaved'));
        return redirect()->back()->with('success', trans('question.MinimumScoreChanged'));
    }

    public function show(Camp $camp)
    {
        Common::authenticate_camp($camp);
        $question_set = $camp->question_set;
        $json = $question_set ? QuestionManager::getQuestionJSON($camp->id, $encode = true) : [];
        View::share('object', $question_set);
        View::share('question_types', QuestionType::getLocalizedConstants('question'));
        View::share('camp', $camp);
        return view('questions.index', compact('json'));
    }

    public function question_set_auto_ranked(QuestionSet $question_set)
    {
        $question_set->update([
            'auto_ranked' => false,
        ]);
    }

    public function finalize(Camp $camp)
    {
        Common::authenticate_camp($camp);
        $question_set = $camp->question_set;
        if (!$question_set || $question_set->pairs->isEmpty())
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoQuestionsSave'));
        if ($question_set->finalized)
            throw new \CampPASSExceptionRedirectBack(trans('exception.QuestionSetAlreadyFinalize'));
        $question_set->update([
            'finalized' => true,
        ]);
        return redirect()->back()->with('success', trans('exception.QuestionSetFinalize'));
    }
}
