<?php

namespace App\Http\Controllers;

use App\Common;
use App\QuestionSet;
use App\Registration;

use App\Enums\RegistrationStatus;

use Illuminate\Http\Request;

class QualificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    public function answer_view($registration_id, $question_set_id)
    {
        $registration = Registration::findOrFail($registration_id);
        $camper = $registration->camper();
        if (!$camper->isCamper())
            return redirect('/')->with('error', 'app.InternalError');
        if ($registration->status == RegistrationStatus::DRAFT || $registration->status == RegistrationStatus::RETURNED)
            return redirect()->back()->with('error', 'You cannot view the answers of an unsubmitted form.');
        $question_set = QuestionSet::findOrFail($question_set_id);
        $camp = $question_set->camp();
        $pairs = $question_set ? $question_set->pairs()->get() : [];
        $data = [];
        $json = Common::getQuestionJSON($question_set->camp_id);
        foreach ($pairs as $pair) {
            $question = $pair->question();
            $answer = $question_set->answers()->where('camper_id', $camper->id)->where('question_id', $question->id)->get();
            if ($answer->isNotEmpty())
                $answer = $answer->first()->answer;
            else
                $answer = '';
            $data[] = [
                'question' => $question,
                'answer' => Common::decodeIfNeeded($answer, $question->type),
            ];
        }
        return view('qualification.answer_view', compact('camper', 'data', 'json'));
    }
}
