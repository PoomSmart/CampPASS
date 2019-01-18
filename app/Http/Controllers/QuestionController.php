<?php

namespace App\Http\Controllers;

use App\Camp;
use App\Question;
use App\QuestionSet;
use App\QuestionSetQuestionPair;

use App\Enums\QuestionType;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->camp = Camp::find($id);
        if (!$this->camp->approved)
            return redirect()->back()->with('error', trans('camp.ApproveFirst'));
        if (!\Auth::user()->canManageCamp($this->camp))
            return redirect()->back()->with('error', trans('app.NoPermissionError'));
        $questionSet = QuestionSet::where('camp_id', $this->camp->id)->first();
        $pairs = $questionSet ? QuestionSetQuestionPair::where('queset_id', $questionSet->id)->get(['que_id']) : [];
        $questions = Question::whereIn('id', $pairs);
        View::share('question_types', $this->question_types);
        return view('questions.index', compact('questions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
