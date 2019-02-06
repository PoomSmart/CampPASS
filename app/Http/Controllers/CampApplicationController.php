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
    public static function authenticate($camp)
    {
        if (!$camp instanceof \App\Camp)
            $camp = Camp::find($camp);
        // Campers would not submit the answers to the questions of such non-approved camps
        if (!$camp->approved && !\Auth::user()->isAdmin())
            throw new \App\Exceptions\ApproveCampException();
        \Auth::user()->isEligibleForCamp($camp);
        return $camp;
    }

    public static function getApplyButtonInformation(Camp $camp, $short = false)
    {
        $apply_text = null;
        $camper = \Auth::user();
        $disabled = false;
        if ($camper) {
            $disabled |= $camper->isAdmin() || $camper->isCampMaker();
            $ineligible_reason = $camper->getIneligibleReasonForCamp($camp, $short);
            if ($ineligible_reason) {
                $disabled = true;
                $apply_text = $ineligible_reason;
            } else {
                $registration = $camper->registrationForCamp($camp);
                $status = $registration ? $registration->status : -1;
                switch ($status) {
                    case RegistrationStatus::DRAFT:
                    case RegistrationStatus::RETURNED:
                        $apply_text = $camp->camp_procedure()->candidate_required ? trans('registration.Edit') : null;
                        break;
                    case RegistrationStatus::APPLIED:
                        $apply_text = trans('registration.APPLIED');
                        break;
                    case RegistrationStatus::APPROVED:
                        $apply_text = trans('registration.APPROVED');
                        break;
                    case RegistrationStatus::QUALIFIED:
                        $apply_text = trans('registration.QUALIFIED');
                        break;
                }
                $disabled |= $status >= RegistrationStatus::APPLIED;
            }
        }
        if (!$apply_text) $apply_text = trans('registration.Apply');
        return [ 'text' => $apply_text, 'disabled' => $disabled, ];
    }

    public function landing(Camp $camp)
    {
        $this->authenticate($camp);
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
                throw new \App\Exceptions\CampPASSException($ineligible_reason);
            $question_set = $camp->question_set();
            $pairs = $question_set ? $question_set->pairs()->get() : [];
            if (empty($pairs))
                throw new \App\Exceptions\CampPASSException('There are no questions in here.');
            $answers = [];
            $json = Common::getQuestionJSON($camp->id);
            $json['answer'] = [];
            $json['answer_id'] = [];
            $pre_answers = Answer::where('question_set_id', $question_set->id)->where('camper_id', $user->id)->get(['id', 'question_id', 'answer']);
            foreach ($pre_answers as $pre_answer) {
                $question = Question::find($id = $pre_answer->question_id);
                $key = $question->json_id;
                $json['answer'][$key] = Common::decodeIfNeeded($pre_answer->answer, $question->type);
                $json['answer_id'][$key] = $pre_answer->id;
            }
            return view('camp_application.question_answer', compact('camp', 'json', 'question_set'));
        }
        // Stage: Apply (right away)
        return $this->submit_application_form($camp);
    }

    public function store(Request $request)
    {
        $camp = $this->authenticate($request['camp_id']);
        $user = \Auth::user();
        // In case campers somehow want to edit the answers in the submitted application form
        if ($user->alreadyAppliedForCamp($camp))
            throw new \App\Exceptions\CampPASSException('Unable to save the answers.');
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
                    // We store the file uploaded by a camper to the folder of the camp with the current question set
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
        $camp = $question_set->camp();
        $this->authenticate($camp);
        $camper = \Auth::user();
        $pairs = $question_set ? $question_set->pairs()->get() : [];
        $data = [];
        $json = Common::getQuestionJSON($question_set->camp_id);
        $answers = $question_set->answers()->where('camper_id', $camper->id)->get();
        foreach ($pairs as $pair) {
            $question = $pair->question();
            $answer = $answers->filter(function ($answer) use ($question) {
                return $answer->question_id == $question->id;
            })->first();
            $answer = $answer ? $answer->first()->answer : '';
            $data[] = [
                'question' => $question,
                'answer' => Common::decodeIfNeeded($answer, $question->type),
            ];
        }
        return view('camp_application.answer_view', compact('data', 'json', 'camp'));
    }

    public function submit_application_form(Camp $camp)
    {
        $this->authenticate($camp);
        $registration = $camp->getLatestRegistration(\Auth::user()->id);
        if ($registration->cannotSubmit()) {
            // This should not happen
            throw new \App\Exceptions\CampPASSException('You cannot submit the application form to the camp you alraedy are qualified for.');
        }
        $registration->status = RegistrationStatus::APPLIED;
        $registration->save();
        return view('camp_application.done');
    }

    public function get_answer_file_path(Answer $answer)
    {
        $question = $answer->question();
        if ($question->type != QuestionType::FILE)
            return null;
        $json_id = $question->json_id;
        $question_set = $question->pair()->question_set();
        $camp = $question_set->camp();
        $camper_id = $answer->camper_id;
        $directory = Common::questionSetDirectory($camp->id);
        $filepath = "{$directory}/{$json_id}/{$camper_id}/{$json_id}.pdf";
        return $filepath;
    }

    public function file_download(Answer $answer)
    {
        $filepath = $this->get_answer_file_path($answer);
        // TODO: check if fallback works properly
        return $filepath ? Storage::download($filepath) : response()->toJson();
    }

    public function answer_file_delete(Answer $answer)
    {
        $filepath = $this->get_answer_file_path($answer);
        if (!$filepath)
            return redirect()->back()->with('error', 'Error deleting the file.');
        Storage::disk('local')->delete($filepath);
        $answer->delete();
        return redirect()->back()->with('success', 'File deleted successfully.');
    }
}
