<?php

namespace App\Http\Controllers;

use App\Common;
use App\Camp;
use App\Question;
use App\QuestionSet;
use App\QuestionSetQuestionPair;

use App\Enums\QuestionType;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    protected $camp;

    function __construct()
    {
        $this->middleware('permission:question-list');
        $this->middleware('permission:question-create', ['only' => ['create', 'show', 'store']]);
        $this->middleware('permission:question-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:question-delete', ['only' => ['destroy']]);
        $types = QuestionType::getConstants();
        $this->question_types = [];
        foreach($types as $text => $number) {
            array_push($this->question_types, (object)[ 'value' => $number, 'name' => trans("question.${text}") ]);
        }
    }

    /**
     * Check whether the given camp can be manipulated by the current user.
     * The function returns the camp object if the user can.
     * 
     */
    private function authenticate($id)
    {
        $camp = Camp::find($id);
        if (!$camp->approved)
            return redirect()->back()->with('error', trans('camp.ApproveFirst'));
        if (!\Auth::user()->canManageCamp($camp))
            return redirect()->back()->with('error', trans('app.NoPermissionError'));
        return $camp;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return null;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $camp = $this->authenticate($request->input('camp_id'));
        if (!get_class($camp) == 'Camp') return $camp;
        $content = $request->all();
        $content = array_splice($content, 1);
        $json = json_encode($content);
        $directory = Common::questionSetDirectory($camp->id);
        Storage::disk('local')->put($directory.'/questions.json', $json);
        return redirect()->back()->with('success', 'Questions saved successfully.');
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
        if (!get_class($camp) == 'Camp') return $camp;
        $camp_id = $camp->id;
        $questionSet = QuestionSet::where('camp_id', $camp_id)->first();
        $pairs = $questionSet ? QuestionSetQuestionPair::where('queset_id', $questionSet->id)->get(['que_id']) : [];
        $questions = Question::whereIn('id', $pairs);
        View::share('question_types', $this->question_types);
        return view('questions.index', compact('questions', 'camp_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return null;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return null;
    }
}
