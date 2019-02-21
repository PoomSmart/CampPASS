<?php

namespace App\Http\Controllers;

use App\Common;
use App\Camp;
use App\Question;
use App\QuestionSet;
use App\QuestionSetQuestionPair;

use App\Http\Requests\StoreQuestionRequest;

use App\Enums\QuestionType;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class QuestionSetController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:question-list');
        $this->middleware('permission:question-create', ['only' => ['show', 'store']]);
        $this->middleware('permission:question-edit', ['only' => ['finalize']]);
        $this->question_types = QuestionType::getLocalizedConstants('question');
    }
    
    public function store(StoreQuestionRequest $request, Camp $camp)
    {
        Common::authenticate_camp($camp);
        $content = $request->all();
        $question_set = QuestionSet::updateOrCreate([
            'camp_id' => $camp->id,
        ], [
            'score_threshold' => $request->input('score_threshold'),
        ]);
        // TODO: How much could we allow for camp makers to make changes on the question set beside the score threshold ?
        if (!$question_set->finalized) {
            $questions = $content['type'];
            $question_set_total_score = 0;
            foreach ($questions as $json_id => $type) {
                $graded = isset($content['question_graded'][$json_id]);
                $question = Question::updateOrCreate([
                    'json_id' => $json_id,
                ], [
                    'type' => (int)$type,
                    'full_score' => $graded ? 10.0 : null,
                ]);
                QuestionSetQuestionPair::updateOrCreate([
                    'question_set_id' => $question_set->id,
                    'question_id' => $question->id,
                ]);
                $question_set_total_score += $question->full_score;
            }
            $question_set->update([
                'total_score' => $question_set_total_score,
            ]);
            // We do not need token to be stored
            unset($content['_token']);
            $content['camp_id'] = $camp->id;
            $json = json_encode($content);
            $directory = Common::questionSetDirectory($camp->id);
            Storage::disk('local')->put($directory.'/questions.json', $json);
            return redirect()->back()->with('success', 'Questions are saved successfully.');
        }
        return redirect()->back()->with('success', 'Score threshold has been changed.');
    }

    public function show(Camp $camp)
    {
        Common::authenticate_camp($camp);
        $question_set = $camp->question_set();
        if ($question_set) {
            // Questions for this camp exist in the database
            $json_path = Common::questionSetDirectory($camp->id).'/questions.json';
            $json = json_encode(Storage::disk('local')->get($json_path));
        } else
            $json = [];
        View::share('object', $question_set);
        View::share('question_types', $this->question_types);
        View::share('camp_id', $camp->id);
        return view('questions.index', compact('json'));
    }

    public function finalize(Camp $camp)
    {
        Common::authenticate_camp($camp);
        $question_set = $camp->question_set();
        // This should not happen
        if (!$question_set)
            throw new \CampPASSException();
        if ($question_set->finalized)
            throw new \CampPASSExceptionRedirectBack('This question set has already been finalized.');
        $question_set->update([
            'finalized' => true,
        ]);
        return redirect()->back()->with('success', 'This question set has been finalized.');
    }
}
