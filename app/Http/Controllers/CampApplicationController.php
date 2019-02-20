<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Camp;
use App\User;
use App\Common;
use App\Registration;
use App\Question;
use App\QuestionSet;
use App\QuestionSetQuestionPair;

use App\BadgeController;
use App\Http\Controllers\QuestionSetController;

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
    public static function authenticate($camp, $eligible_check = false)
    {
        if (!$camp instanceof \App\Camp)
            $camp = Camp::find($camp);
        // Campers would not submit the answers to the questions of such non-approved camps
        if (!$camp->approved && !\Auth::user()->isAdmin())
            throw new \App\Exceptions\ApproveCampException();
        if ($eligible_check)
            \Auth::user()->isEligibleForCamp($camp);
        return $camp;
    }

    /**
     * Given a camp and the current user, determine the registration status and return the apply button's status and availability.
     * 
     */
    public static function getApplyButtonInformation(Camp $camp, $short = false)
    {
        $apply_text = null;
        $camper = \Auth::user();
        $disabled = false;
        $route = null;
        if ($camper) {
            $disabled |= $camper->isAdmin() || $camper->isCampMaker();
            $ineligible_reason = $camper->getIneligibleReasonForCamp($camp, $short);
            if ($ineligible_reason) {
                $disabled = true;
                $apply_text = $ineligible_reason;
            } else if ($camper->isCamper()) {
                $registration = $camper->getLatestRegistrationForCamp($camp);
                $status = $registration ? $registration->status : -1;
                $camp_procedure = $camp->camp_procedure();
                switch ($status) {
                    case RegistrationStatus::DRAFT:
                    case RegistrationStatus::RETURNED:
                        $apply_text = $camp_procedure->candidate_required ? trans('registration.Edit') : null;
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
                // We allow the button to show as "Pay Deposit" if there are further stages, and Deposit Only is our exception
                if ($camp_procedure->deposit_required && $camp_procedure->candidate_required) {
                    $apply_text = trans('registration.PayDeposit');
                    $route = 'camp_application.deposit';
                } else
                    $disabled |= $status >= RegistrationStatus::APPLIED;
            }
        }
        if (!$apply_text) {
            $apply_text = trans('registration.Apply');
            $camp_procedure = $camp->camp_procedure();
            if ($camp_procedure->candidate_required)
                $apply_text = "{$apply_text} (QA)";
        }
        return [
            'text' => $apply_text,
            'disabled' => $disabled,
            'route' => $route,
        ];
    }

    /**
     * Create a registration record given the user and the camp with an optional parameter, registration status,
     * in case we know exactly the registration status to set.
     * 
     */
    public function register(Camp $camp, User $user, $status = RegistrationStatus::DRAFT, bool $badge_check = false)
    {
        $ineligible_reason = $user->getIneligibleReasonForCamp($camp);
        if ($ineligible_reason)
            throw new \CampPASSException($ineligible_reason);
        $registration = $camp->getLatestRegistration($user);
        if ($registration) {
            if ($registration->qualified())
                throw new \CampPASSException('You already have applied for this camp.');
            if ($status != RegistrationStatus::DRAFT) {
                $registration->update([
                    'status' => $status,
                    'submission_time' => now(),
                ]);
            }
        } else {
            $registration = Registration::create([
                'camp_id' => $camp->id,
                'camper_id' => $user->id,
                'status' => $status,
                'submission_time' => now(),
            ]);
        }
        if ($badge_check)
            BadgeController::addBadgeIfNeeded($registration);
        return $registration;
    }

    /**
     * For the camps that include questions, we fetch those questions and respective answers for campers (if any) and return as JSON.
     * 
     */
    public function prepare_questions_answers(Camp $camp, User $user)
    {
        $question_set = $camp->question_set();
        $pairs = $question_set ? $question_set->pairs()->get() : null;
        if (!isset($pairs) || $pairs->isEmpty())
            throw new \CampPASSException('There are no questions in here.');
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
        return [
            'json' => $json,
            'question_set' => $question_set,
        ];
    }

    public function landing(Camp $camp)
    {
        $this->authenticate($camp);
        $user = \Auth::user();
        $registration = $this->register($camp, $user);
        $camp_procedure = $camp->camp_procedure();
        if ($registration->applied_or_qualified()) {
            // Stage: Already applied or qualified
            if ($registration->qualified())
                throw new \CampPASSExceptionRedirectBack('You already are qualified for this camp.');
            if ($camp_procedure->deposit_required) {
                if ($camp_procedure->candidate_required) {
                    if ($registration->approved()) {
                        // TODO: Stage: Status checking after being approved
                        // Cases: QA & Deposit / QA & Interview & Deposit Approved
                        // The view is only for chosen candidates
                    }
                    // Stage: Upload payment slip after the application
                    // Cases: QA & Deposit / QA & Interview & Deposit Applied
                    return view('camp_application.deposit', compact('camp'));
                }
                // TODO: Stage: Status checking
                // Cases: Deposit Only / QA Only Applied
                // The view is only for chosen candidates
            }
            if ($camp_procedure->interview_required) {
                // TODO: Stage: Status checking for further interview
                // Cases: QA & Interview Applied
                // The view is only for chosen candidates
            }
            throw new \CampPASSExceptionRedirectBack('You already have applied for this camp.');
        }
        if ($camp_procedure->candidate_required) {
            // Stage: Answering questions
            // Cases: All camp procedures with Questions Pre-applied
            $data = $this->prepare_questions_answers($camp, $user);
            $json = $data['json'];
            $question_set = $data['question_set'];
            return view('camp_application.question_answer', compact('camp', 'json', 'question_set'));
        }
        if ($camp_procedure->deposit_required) {
            // Stage: Upload payment slip
            // Cases: Deposit Only Pre-applied
            // TODO: The below view is only for chosen candidates
            return view('camp_application.deposit', compact('camp'));
        }
        // Stage: Apply (right away)
        // Cases: Walk-in Pre-applied
        // TODO: with status checking page?
        return $this->submit_application_form($camp);
    }

    public function store(Request $request)
    {
        $camp = $this->authenticate($request['camp_id']);
        $user = \Auth::user();
        // A registration record will be created if not already
        $registration = $this->register($camp, $user);
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

    /**
     * Display the review answer page for campers.
     * 
     */
    public function answer_view(QuestionSet $question_set)
    {
        $camp = $question_set->camp();
        $this->authenticate($camp, $eligible_check = true);
        $camper = \Auth::user();
        $pairs = $question_set ? $question_set->pairs()->get() : [];
        $data = [];
        $json = Common::getQuestionJSON($question_set->camp_id);
        $answers = $question_set->answers()->where('camper_id', $camper->id)->get();
        if ($answers->isEmpty())
            throw new \CampPASSExceptionRedirectBack('You have not answered anything.');
        foreach ($answers as $answer) {
            $question = $answer->question();
            $data[] = [
                'question' => $question,
                'answer' => Common::decodeIfNeeded($answer->answer, $question->type),
            ];
        }
        return view('camp_application.answer_view', compact('data', 'json', 'camp'));
    }

    /**
     * Directly apply for a camp and respond back with the done page.
     * 
     */
    public function submit_application_form(Camp $camp)
    {
        $this->authenticate($camp);
        $this->register($camp, $user = \Auth::user(), $status = RegistrationStatus::APPLIED, $badge_check = true);
        return view('camp_application.done');
    }

    public function deposit(Registration $registration)
    {
        // TODO: complete this
        return view('camp_application.deposit');
    }

    /**
     * Make sure the only answer owner and respective camp makers can access the answer file.
     * 
     */
    public function canAccessAnswer(Answer $answer)
    {
        $user = \Auth::user();
        if ($user->isAdmin())
            return;
        if ($user->isCamper() && $answer->camper()->id != $user->id)
            throw new \CampPASSExceptionPermission();
        else if ($user->isCampMaker() && !$user->canManageCamp($answer->question_set()->camp()))
            throw new \CampPASSExceptionPermission();
    }

    /**
     * Get the respective file path of the given answer.
     * 
     */
    public function get_answer_file_path(Answer $answer)
    {
        $this->canAccessAnswer($answer);
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

    /**
     * Download the answer of type file.
     * 
     */
    public function answer_file_download(Answer $answer)
    {
        $filepath = $this->get_answer_file_path($answer);
        // TODO: check if fallback works properly
        return $filepath ? Storage::download($filepath) : response()->toJson();
    }

    /**
     * Delete the answer of type file.
     * 
     */
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
