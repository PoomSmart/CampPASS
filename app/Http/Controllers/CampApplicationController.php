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
    public static function authenticate(Camp $camp, bool $eligible_check = false)
    {
        $user = \Auth::user();
        if (!$user)
            throw new \CampPASSExceptionPermission();
        if (!$user->hasPermissionTo('answer-list'))
            throw new \CampPASSExceptionRedirectBack(trans('app.NoPermissionError'));
        // Campers would not submit the answers to the questions of such non-approved camps
        if (!$camp->approved && !\Auth::user()->isAdmin())
            throw new \App\Exceptions\ApproveCampException();
        if ($eligible_check)
            \Auth::user()->isEligibleForCamp($camp);
        return $camp;
    }

    /**
     * Check whether the current user can manipulate the given registration record.
     * That is, only the owner can make changes.
     * 
     */
    public static function authenticate_registration(Registration $registration, bool $silent = false)
    {
        if (!$silent && $registration->camper->id != \Auth::user()->id)
            throw new \CampPASSExceptionPermission();
    }

    /**
     * Given a camp and the current user, determine the registration status and return the apply button's status and availability.
     * 
     */
    public static function getApplyButtonInformation(Camp $camp, bool $short = false)
    {
        $apply_text = null;
        $user = \Auth::user();
        $disabled = false;
        $route = null;
        if ($user) {
            $disabled |= $user->isAdmin() || $user->isCampMaker();
            $ineligible_reason = $user->getIneligibleReasonForCamp($camp, $short);
            if ($ineligible_reason) {
                $disabled = true;
                $apply_text = $ineligible_reason;
            } else if ($user->isCamper()) {
                $registration = $user->getLatestRegistrationForCamp($camp);
                if ($registration) {
                    $apply_text = trans('registration.ApplicationStatus');
                    $route = route('camp_application.status', $registration->id);
                }
            }
        }
        if (!$apply_text) {
            $apply_text = trans('registration.Apply');
            $camp_procedure = $camp->camp_procedure;
            if ($camp_procedure->candidate_required)
                $apply_text = "{$apply_text} (QA)";
        }
        if (!$route)
            $route = route('camp_application.landing', $camp->id);
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
    public static function register(Camp $camp, User $user, $status = RegistrationStatus::DRAFT, bool $badge_check = false)
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
    public static function prepare_questions_answers(Camp $camp, User $user)
    {
        $question_set = $camp->question_set;
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

    public static function landing(Camp $camp, Registration $registration = null)
    {
        self::authenticate($camp);
        $user = \Auth::user();
        if (!$registration)
            $registration = self::register($camp, $user);
        $camp_procedure = $camp->camp_procedure;
        // Stage: Already applied or qualified
        if ($registration->applied_to_qualified())
            return self::status($registration);
        if ($camp_procedure->candidate_required) {
            // Stage: Answering questions
            // Cases: All camp procedures with Questions Pre-applied
            $data = self::prepare_questions_answers($camp, $user);
            $json = $data['json'];
            $question_set = $data['question_set'];
            return view('camp_application.question_answer', compact('camp', 'json', 'question_set'));
        }
        if ($camp_procedure->deposit_required) {
            // Stage: Upload payment slip
            // Cases: Deposit Only Pre-applied
            return self::status($registration);
        }
        // Stage: Apply (right away)
        // Cases: Walk-in Pre-applied
        return self::submit_application_form($camp, $status = RegistrationStatus::APPROVED);
    }

    public function store(Request $request)
    {
        $camp = $this->authenticate($request['camp_id']);
        $user = \Auth::user();
        if (!$user->hasPermissionTo('answer-edit') || !$user->hasPermissionTo('answer-create'))
            throw new \CampPASSExceptionRedirectBack(trans('app.NoPermissionError'));
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
        $camp = $question_set->camp;
        $this->authenticate($camp, $eligible_check = true);
        $camper = \Auth::user();
        $pairs = $question_set ? $question_set->pairs()->get() : [];
        $data = [];
        $json = Common::getQuestionJSON($question_set->camp_id);
        $answers = $question_set->answers()->where('camper_id', $camper->id)->get();
        if ($answers->isEmpty())
            throw new \CampPASSExceptionRedirectBack('You have not answered anything.');
        foreach ($answers as $answer) {
            $question = $answer->question;
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
    public static function submit_application_form(Camp $camp, $status = RegistrationStatus::APPLIED)
    {
        self::authenticate($camp);
        self::register($camp, $user = \Auth::user(), $status = $status, $badge_check = true);
        return view('camp_application.done');
    }

    public static function status(Registration $registration)
    {
        self::authenticate($registration->camp);
        self::authenticate_registration($registration);
        return view('camp_application.status', compact('registration'));
    }

    public static function confirm(Registration $registration, bool $void = false)
    {
        self::authenticate($registration->camp);
        self::authenticate_registration($registration, $silent = $void);
        if ($registration->status == RegistrationStatus::QUALIFIED)
            throw new \CampPASSExceptionRedirectBack('You already confirmed attending this camp.');
        $registration->update([
            'status' => RegistrationStatus::QUALIFIED,
        ]);
        BadgeController::addBadgeIfNeeded($registration);
        if (!$void)
            return redirect()->back()->with('success', 'You are fully qualified for this camp.');
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
        if ($user->isCamper() && $answer->camper->id != $user->id)
            throw new \CampPASSExceptionPermission();
        else if ($user->isCampMaker() && !$user->canManageCamp($answer->question_set->camp))
            throw new \CampPASSExceptionPermission();
    }

    /**
     * Get the respective file path of the given answer.
     * 
     */
    public function get_answer_file_path(Answer $answer)
    {
        $this->canAccessAnswer($answer);
        $question = $answer->question;
        if ($question->type != QuestionType::FILE)
            return null;
        $json_id = $question->json_id;
        $question_set = $question->pair->question_set;
        $camp = $question_set->camp;
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
        if (!\Auth::user()->hasPermissionTo('answer-delete'))
            throw new \CampPASSExceptionRedirectBack(trans('app.NoPermissionError'));
        $filepath = $this->get_answer_file_path($answer);
        if (!$filepath)
            return redirect()->back()->with('error', 'Error deleting the file.');
        Storage::disk('local')->delete($filepath);
        $answer->delete();
        return redirect()->back()->with('success', 'File deleted successfully.');
    }
}
