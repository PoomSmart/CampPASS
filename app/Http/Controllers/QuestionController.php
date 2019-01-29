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

class QuestionController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:question-list');
        $this->middleware('permission:question-create', ['only' => ['create', 'show', 'store']]);
        $this->middleware('permission:question-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:question-delete', ['only' => ['destroy']]);
        $types = QuestionType::getConstants();
        $this->question_types = [];
        foreach ($types as $text => $number) {
            array_push($this->question_types, (object)[ 'value' => $number, 'name' => trans("question.${text}") ]);
        }
    }

    /**
     * Check whether the given camp can be manipulated by the current user.
     * The function returns the camp object if the user can.
     * 
     */
    private function authenticate($camp_id)
    {
        $camp = Camp::find($camp_id);
        if (!$camp->approved && !\Auth::user()->hasRole('admin'))
            return redirect('/')->with('error', trans('camp.ApproveFirst'));
        if (!\Auth::user()->canManageCamp($camp))
            return redirect('/')->with('error', trans('app.NoPermissionError'));
        return $camp;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Https\Requests\StoreQuestionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuestionRequest $request)
    {
        $camp = $this->authenticate($request->input('camp_id'));
        if (strcmp(get_class($camp), 'App\Camp')) return $camp;
        $content = $request->all();
        $question_set_id = QuestionSet::updateOrCreate([
            'camp_id' => $camp->id,
        ], [
            'score_threshold' => $request->input('score_threshold'),
        ])->id;
        $questions = $content['type'];
        foreach ($questions as $json_id => $type) {
            $graded = isset($content['question_graded'][$json_id]);
            $question_id = Question::updateOrCreate([
                'json_id' => $json_id,
            ], [
                'type' => (int)$type,
                'full_score' => $graded ? 10.0 : null, // TODO: user-specified?
            ])->id;
            QuestionSetQuestionPair::updateOrCreate([
                'question_set_id' => $question_set_id,
                'question_id' => $question_id,
            ]);
        }
        // We do not need token to be stored
        unset($content['_token']);
        $json = json_encode($content);
        $directory = Common::questionSetDirectory($camp->id);
        Storage::disk('local')->put($directory.'/questions.json', $json);
        return redirect()->back()->with('success', 'Questions are saved successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $camp = $this->authenticate($id);
        if (strcmp(get_class($camp), 'App\Camp')) return $camp;
        $camp_id = $camp->id;
        $question_set = QuestionSet::where('camp_id', $camp_id)->first();
        if ($question_set) {
            // questions for this camp exist
            $json_path = Common::questionSetDirectory($camp_id).'/questions.json';
            $json = json_encode(Storage::disk('local')->get($json_path));
        } else
            $json = [];
        View::share('object', $question_set);
        View::share('question_types', $this->question_types);
        View::share('camp_id', $camp_id);
        return view('questions.index', compact('json'));
    }
}
