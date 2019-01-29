<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Camp;
use App\Common;
use App\Registration;
use App\Question;
use App\QuestionSet;
use App\QuestionSetQuestionPair;

use App\Http\Controllers\QuestionController;

use App\Enums\QuestionType;
use App\Enums\RegistrationStatus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampApplicationController extends Controller
{
    /**
     * Check whether the given camp can be manipulated by the current user.
     * The function returns the camp object if the user can.
     * 
     */
    public static function authenticate($camp_id)
    {
        $camp = Camp::find($camp_id);
        if (!$camp->approved && !\Auth::user()->hasRole('admin'))
            return redirect('/')->with('error', trans('camp.ApproveFirst'));
        if (!\Auth::user()->canManageCamp($camp))
            return redirect('/')->with('error', trans('app.NoPermissionError'));
        return $camp;
    }

    public function landing(Camp $camp)
    {
        $user = \Auth::user();
        $already_applied = $user->alreadyAppliedForCamp($camp);
        $camp_procedure = $camp->camp_procedure();
        if ($already_applied) {
            if ($camp_procedure->deposit_required) {
                // Stage: Paying deposit
                return view('camp_application.deposit', compact('camp'));
            }
            // Stage: Applied
            return view('camp_application.question_answer', compact('already_applied'));
        }
        if ($camp_procedure->candidate_required) {
            // Stage: Answering questions
            $ineligible_reason = $user->getIneligibleReasonForCamp($camp);
            if ($ineligible_reason)
                return redirect()->back()->with('error', $ineligible_reason);
            $question_set = $camp->question_set();
            $pairs = $question_set ? $question_set->pairs()->get() : [];
            if (empty($pairs))
                return redirect()->back()->with('error', 'There are no questions in here.');
            $answers = [];
            $json = Common::getQuestionJSON($camp->id);
            $pre_answers = Answer::where('question_set_id', $question_set->id)->where('camper_id', $user->id)->get(['question_id', 'answer']);
            foreach ($pre_answers as $pre_answer) {
                $question = Question::find($id = $pre_answer->question_id);
                $key = $question->json_id;
                $answers[$key] = Common::decodeIfNeeded($pre_answer->answer, $question->type);
            }
            return view('camp_application.question_answer', compact('camp', 'answers', 'json', 'question_set'));
        }
        // Stage: Apply (right away)
        return $this->submit_application_form($camp);
    }

    public function store(Request $request)
    {
        $camp = Camp::find($request['camp_id']);
        if (strcmp(get_class($camp), 'App\Camp')) return $camp;
        $user = \Auth::user();
        // Campers would not submit the answers to the questions of such non-approved camps
        if (!$camp->approved && !$user->isAdmin())
            return redirect('/')->with('error', 'Unable to save the answers.');
        // In case campers somehow want to edit the answers in the submitted application form
        if ($user->alreadyAppliedForCamp($camp))
            return redirect('/')->with('error', 'Unable to save the answers.');
        // A registration record will be created if not already
        $registration = $camp->getLatestRegistration($user->id);
        if (!$registration)
            $registration = Registration::create([
                'camp_id' => $camp->id,
                'camper_id' => $user->id,
            ]);
        // Get the corresponding question set for this camp, then reference it to creating or updating answers as needed
        $question_set = QuestionSet::where('camp_id', $camp->id)->first();
        $question_ids = $question_set->pairs()->get(['question_id']);
        $questions = Question::whereIn('id', $question_ids)->get();
        foreach ($questions as $question) {
            $json_id = $question->json_id;
            $answer_content = null;
            if ($question->type == QuestionType::FILE) {
                if ($request->hasFile($json_id)) {
                    $file_post = $request->file($json_id);
                    $answer_content = $file_post->getClientOriginalName();
                    $file = $request->file($json_id);
                    $directory = Common::questionSetDirectory($camp->id);
                    Storage::disk('local')->putFileAs("{$directory}/{$json_id}/{$user->id}", $file, "{$json_id}.pdf");
                }
            } else
                $answer_content = Common::encodeIfNeeded($request[$json_id], $question->type);
            if ($question->type == QuestionType::FILE && !$answer_content)
                continue;
            Answer::updateOrCreate([
                'question_set_id' => $question_set->id,
                'question_id' => $question->id,
                'camper_id' => $user->id,
                'registration_id' => $registration->id,
            ], [
                'answer' => $answer_content,
            ]);
        }
        return redirect()->back()->with('success', 'Answers are saved.');
    }

    public function answer_view(QuestionSet $question_set)
    {
        $camper = \Auth::user();
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
        return view('camp_application.answer_view', compact('data', 'json', 'question_set', 'camp'));
    }

    public function submit_application_form(Camp $camp)
    {
        $registration = $camp->getLatestRegistration(\Auth::user()->id);
        if ($registration->cannotSubmit()) {
            // This should not happen
            return redirect()->back()->with('error', 'You cannot submit the application form to the camp you alraedy are qualified for.');
        }
        $registration->status = RegistrationStatus::APPLIED;
        $registration->save();
        return view('camp_application.done');
    }

    public function get_question($json_id)
    {
        $question = Question::where('json_id', $json_id)->first();
        if (!$question)
            return null;
        if ($question->type != QuestionType::FILE)
            return null;
        return $question;
    }

    public function get_file_path($json_id)
    {
        $question = $this->get_question($json_id);
        if (!$question)
            return null;
        $question_set = $question->pair()->question_set();
        $camp = $question_set->camp();
        $directory = Common::questionSetDirectory($camp->id);
        $user = \Auth::user();
        $filepath = "{$directory}/{$json_id}/{$user->id}/{$json_id}.pdf";
        return $filepath;
    }

    public function file_download($json_id)
    {
        $filepath = $this->get_file_path($json_id);
        // TODO: check if fallback works properly
        return $filepath ? Storage::download($filepath) : response()->toJson();
    }

    public function file_delete($json_id)
    {
        // TODO: this is somewhat boilerplate
        $filepath = $this->get_file_path($json_id);
        if (!$filepath)
            return redirect()->back()->with('error', 'Error deleting the file.');
        Storage::disk('local')->delete($filepath);
        $question = $this->get_question($json_id);
        if ($question) {
            $question_set = $question->pair()->question_set();
            $answer = $question_set->answers()->where('camper_id', \Auth::user()->id)->where('question_id', $question->id)->first();
            $answer->delete();
        }
        return redirect()->back()->with('success', 'File deleted successfully.');
    }
}
