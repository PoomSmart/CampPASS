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
use App\QuestionManager;

use Carbon\Carbon;

use App\BadgeController;
use App\Http\Controllers\QuestionSetController;

use App\Enums\QuestionType;
use App\Enums\ApplicationStatus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampApplicationController extends Controller
{
    /**
     * Check whether the given camp can be manipulated by the current user.
     * 
     */
    public static function authenticate(Camp $camp)
    {
        $user = auth()->user();
        if (!$user)
            throw new \CampPASSExceptionPermission();
        if (!$user->hasPermissionTo('answer-list'))
            throw new \CampPASSExceptionRedirectBack(trans('app.NoPermissionError'));
        // Campers would not submit the answers to the questions of such non-approved camps
        if (!$camp->approved && !$user->isAdmin())
            throw new \App\Exceptions\ApproveCampException();
        if (!$user->isAdmin())
            $user->isEligibleForCamp($camp);
    }

    /**
     * Check whether the current user can manipulate the given registration record.
     * That is, only the owner can make changes.
     * 
     */
    public static function authenticate_registration(Registration $registration, bool $silent = false)
    {
        if (!$silent && $registration->camper->id != auth()->user()->id && !auth()->user()->isAdmin())
            throw new \CampPASSExceptionPermission();
    }

    /**
     * Given a camp and the current user, determine the registration status and return the apply button's status and availability.
     * 
     */
    public static function getApplyButtonInformation(Camp $camp, bool $short = false)
    {
        $apply_text = null;
        $user = auth()->user();
        $disabled = false;
        $route = null;
        if ($user) {
            $non_campers = $user->isAdmin() || $user->isCampMaker();
            $disabled |= $non_campers;
            $ineligible_reason = $non_campers ? null : $user->getIneligibleReasonForCamp($camp, $short);
            if ($ineligible_reason) {
                $disabled = true;
                $apply_text = $ineligible_reason;
            } else if ($user->isCamper()) {
                $registration = $user->getLatestRegistrationForCamp($camp);
                if ($registration) {
                    $apply_text = trans('registration.Status');
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
    public static function register(Camp $camp, User $user, $status = ApplicationStatus::DRAFT, bool $badge_check = false)
    {
        $ineligible_reason = $user->getIneligibleReasonForCamp($camp);
        if ($ineligible_reason)
            throw new \CampPASSException($ineligible_reason);
        $registration = $camp->getLatestRegistration($user);
        if ($registration) {
            if ($registration->confirmed())
                throw new \CampPASSException(trans('exception.AlreadyAppliedCamp'));
            if ($status != ApplicationStatus::DRAFT) {
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
    public static function prepare_questions_answers(Camp $camp)
    {
        $question_set = $camp->question_set;
        $pairs = $question_set ? $question_set->pairs()->get() : null;
        if (!isset($pairs) || $pairs->isEmpty())
            throw new \CampPASSException(trans('exception.NoQuestion'));
        $user = auth()->user();
        $answers = [];
        $json = QuestionManager::getQuestionJSON($camp->id);
        $json['answer'] = [];
        $json['answer_id'] = [];
        $pre_answers = Answer::where('question_set_id', $question_set->id)->where('camper_id', $user->id)->get(['id', 'question_id', 'answer']);
        foreach ($pre_answers as $pre_answer) {
            $question = Question::find($id = $pre_answer->question_id);
            $key = $question->json_id;
            $json['answer'][$key] = QuestionManager::decodeIfNeeded($pre_answer->answer, $question->type);
            $json['answer_id'][$key] = $pre_answer->id;
        }
        return view('camp_application.question_answer', compact('camp', 'json', 'question_set'));
    }

    public static function landing(Camp $camp, Registration $registration = null)
    {
        self::authenticate($camp);
        $user = auth()->user();
        if (!$registration)
            $registration = self::register($camp, $user);
        $camp_procedure = $camp->camp_procedure;
        // If campers already submitted the form, let they see their application status
        if ($registration->submitted())
            return self::status($registration);
        if ($camp_procedure->candidate_required) {
            // Stage: Answering questions
            return self::prepare_questions_answers($camp);
        }
        if ($camp_procedure->deposit_required) {
            // Stage: Upload payment slip (Deposit Only)
            // Special case: The registration will be automatically in the chosen state
            $registration->update([
                'status' => ApplicationStatus::CHOSEN,
            ]);
            return self::status($registration);
        }
        // Stage: Apply (right away)
        return self::submit_application_form($camp, $status = ApplicationStatus::CONFIRMED);
    }

    public function store(Request $request)
    {
        $this->authenticate($camp = Camp::find($request['camp_id']));
        $user = auth()->user();
        if (!$user->hasPermissionTo('answer-edit') || !$user->hasPermissionTo('answer-create'))
            throw new \CampPASSExceptionRedirectBack(trans('app.NoPermissionError'));
        // A registration record will be created if not already
        $registration = $this->register($camp, $user);
        if ($registration->rejected() || $registration->withdrawed())
            throw new \CampPASSExceptionRedirectBack(trans('exception.YouAreNoLongerAbleToDoThat'));
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
                    $directory = QuestionManager::questionSetDirectory($camp->id);
                    Storage::disk('local')->putFileAs("{$directory}/{$json_id}/{$user->id}", $file, "{$json_id}.pdf");
                }
            } else
                $answer_content = QuestionManager::encodeIfNeeded($request[$json_id], $question->type);
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
        return redirect()->back()->with('success', trans('message.AnswerSaved'));
    }

    /**
     * Display the review answer page for campers.
     * 
     */
    public function answer_view(QuestionSet $question_set)
    {
        $camp = $question_set->camp;
        $this->authenticate($camp);
        $camper = auth()->user();
        $pairs = $question_set ? $question_set->pairs()->get() : [];
        $data = [];
        $json = QuestionManager::getQuestionJSON($question_set->camp_id);
        $answers = $question_set->answers()->where('camper_id', $camper->id)->get();
        if ($answers->isEmpty())
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoAnswer'));
        foreach ($answers as $answer) {
            $question = $answer->question;
            $data[] = [
                'question' => $question,
                'answer' => QuestionManager::decodeIfNeeded($answer->answer, $question->type),
            ];
        }
        return view('camp_application.answer_view', compact('data', 'json', 'camp'));
    }

    /**
     * Directly apply for a camp and respond back with the done page.
     * 
     */
    public static function submit_application_form(Camp $camp, $status = ApplicationStatus::APPLIED)
    {
        self::authenticate($camp);
        self::register($camp, $user = auth()->user(), $status = $status, $badge_check = true);
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
        $camp = $registration->camp;
        self::authenticate($camp);
        self::authenticate_registration($registration, $silent = $void);
        if ($registration->rejected() || $registration->withdrawed())
            throw new \CampPASSExceptionRedirectBack(trans('exception.YouAreNoLongerAbleToDoThat'));
        if ($registration->confirmed())
            throw new \CampPASSExceptionRedirectBack(trans('exception.ConfirmedAttending', ['camp' => $camp]));
        if ($camp->confirmation_date && Carbon::now()->diffInDays($confirmation_date = Carbon::parse($camp->confirmation_date)) < 0) {
            $form_score = $registration->form_score;
            $prevent = true;
            if ($form_score->backup) {
                $extended_confirmation_date = $confirmation_date->addDays(3);
                $prevent = Carbon::now()->diffInDays($extended_confirmation_date) < 0;
            }
            if ($prevent)
                throw new \CampPASSExceptionRedirectBack(trans('exception.YouAreNoLongerAbleToDoThat'));
        }
        $registration->update([
            'status' => ApplicationStatus::CONFIRMED,
        ]);
        BadgeController::addBadgeIfNeeded($registration);
        if (!$void)
            return redirect()->back()->with('success', trans('message.FullyQualified', ['camp' => $camp]));
    }

    public static function withdraw(Registration $registration)
    {
        $camp = $registration->camp;
        self::authenticate($camp);
        self::authenticate_registration($registration);
        if ($registration->confirmed())
            throw new \CampPASSException(trans("exception.WithdrawAttendance"));
        if ($registration->withdrawed())
            throw new \CampPASSExceptionRedirectBack(trans('exception.AlreadyWithdrawed', ['camp' => $camp]));
        if ($registration->rejected())
            throw new \CampPASSExceptionRedirectBack(trans('exception.YouAreNoLongerAbleToDoThat'));
        $registration->update([
            'status' => ApplicationStatus::WITHDRAWED,
        ]);
        // The corresponding registration record will be automatically marked as not passed, as they lost their chance to join the camp
        $registration->form_score->update([
            'passed' => false,
        ]);
        return redirect()->back()->with('success', trans('exception.WithdrawedFrom', ['camp' => $camp]));
    }

    /**
     * Make sure the only answer owner and respective camp makers can access the answer file.
     * 
     */
    public function canAccessAnswer(Answer $answer)
    {
        $user = auth()->user();
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
        $directory = QuestionManager::questionSetDirectory($camp->id);
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
        try {
            return Storage::download($filepath);
        } catch (\Exception $e) {
            throw new \CampPASSExceptionRedirectBack(trans('exception.FileNotFound'));
        }
    }

    /**
     * Delete the answer of type file.
     * 
     */
    public function answer_file_delete(Answer $answer)
    {
        if (!auth()->user()->hasPermissionTo('answer-delete'))
            throw new \CampPASSExceptionRedirectBack(trans('app.NoPermissionError'));
        $filepath = $this->get_answer_file_path($answer);
        if (!$filepath)
            return redirect()->back()->with('error', 'message.ErrorDelete');
        Storage::disk('local')->delete($filepath);
        $answer->delete();
        return redirect()->back()->with('success', 'message.SuccessDelete');
    }
}
