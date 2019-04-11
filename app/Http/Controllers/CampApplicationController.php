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
use App\Enums\BlockApplicationStatus;

use App\Notifications\CamperStatusChanged;

use App\Http\Requests\StorePDFRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampApplicationController extends Controller
{
    /**
     * Check whether the given camp can be manipulated by the current user.
     *
     */
    public static function authenticate(Camp $camp, bool $silent = false)
    {
        if ($silent) return;
        $user = auth()->user();
        if (!$user)
            throw new \CampPASSExceptionPermission();
        if (!$user->can('answer-list'))
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
     * Make sure the only resource owner and respective camp makers can access it.
     *
     */
    public static function canAccessResource($resource_owner_id, $camp_id)
    {
        $user = auth()->user();
        if ($user->isAdmin())
            return;
        if ($user->isCamper() && $resource_owner_id != $user->id)
            throw new \CampPASSExceptionPermission();
        else if ($user->isCampMaker() && !$user->canManageCamp(Camp::findOrFail($camp_id)))
            throw new \CampPASSExceptionPermission();
    }

    /**
     * Given a camp and the current user, determine the registration status and return the apply button's status and availability.
     *
     */
    public static function getApplyButtonInformation(Camp $camp, bool $short = false, bool $auth_check = false)
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
                $registration = $user->getRegistrationForCamp($camp);
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
                $apply_text = "{$apply_text} (Q&A)";
        }
        if (!$route)
            $route = route($auth_check ? 'camps.show' : 'camp_application.landing', $camp->id);
        return [
            'text' => $apply_text,
            'disabled' => $disabled,
            'route' => $route,
        ];
    }

    public static function statusDescription($step, Registration $registration, Camp $camp, $camp_procedure = null)
    {
        $text = null;
        $button = false;
        $passed = true;
        switch ($step) {
            case BlockApplicationStatus::APPLICATION:
                if ($registration->returned)
                    $text = trans('registration.ReturnedApplication');
                else if ($registration->rejected())
                    $text = trans('registration.RejectedApplication');
                else if ($registration->applied())
                    $text = trans('registration.Grading');
                else if ($registration->chosen() || $registration->approved_to_confirmed())
                    $text = trans('registration.CongratulationsApp');
                else
                    $button = true;
                break;
            case BlockApplicationStatus::INTERVIEW:
                if ($registration->chosen_to_confirmed()) {
                    if ($registration->interviewed_to_confirmed())
                        $text = trans('registration.CongratulationsInterview');
                    else if ($camp->interview_information)
                        $text = trans('camp.InterviewDate').': '.$camp->getInterviewDate().': '.$camp->interview_information;
                } else if ($registration->rejected())
                    $text = trans('registration.RejectedApplication');
                else {
                    $text = trans('qualification.AckInterview');
                    $passed = false;
                }
                break;
            case BlockApplicationStatus::PAYMENT:
                if ($registration->approved_to_confirmed())
                    $text = trans('registration.SlipApproved');
                else {
                    if ($registration->rejected())
                        $text = trans('registration.RejectedApplication');
                    else if (self::get_payment_path($registration)) {
                        if ($registration->returned) {
                            $text = trans('registration.PleaseRecheckSlip');
                            $button = true;
                        } else
                            $text = trans('registration.SlipUploaded');
                    } else if ($registration->chosen()) {
                        $text = trans('registration.UploadPayment');
                        $button = true;
                        if ($camp_procedure->deposit_required) {
                            if ($camp_procedure->interview_required)
                                $button = $registration->interviewed();
                        }
                    } else {
                        $text = trans('registration.AckSlip');
                        $passed = false;
                    }
                }
                break;
            case BlockApplicationStatus::APPROVAL:
                if ($registration->returned) {
                    $text = trans('qualification.DocumentsNeedRecheck');
                    $button = true;
                } else if ($registration->approved_to_confirmed())
                    $text = trans('qualification.DocumentsApproved');
                else if ($registration->chosen())
                    $text = $camp_procedure->depositOnly() ? trans('registration.AckSlip') : trans('qualification.DocumentsInProcess');
                else {
                    $text = trans('qualification.DocumentsWillBeApproved');
                    $passed = false;
                }
                break;
            case BlockApplicationStatus::CONFIRMATION:
                if ($registration->confirmed())
                    $text = trans('qualification.AttendanceConfirmed', ['camp' => $camp]);
                else if ($registration->withdrawed() || $registration->rejected()) {
                    $text = trans('qualification.NotAllowedToConfirm');
                    $passed = false;
                } else if ($registration->approved()
                    && ($camp_procedure->interview_required ? $registration->interviewed_to_confirmed() : true)
                    && ($camp->hasPayment() ? $registration->approved_to_confirmed() : true)) {
                        $button = true;
                        $text = trans('qualification.AttendanceConfirmed', ['camp' => $camp]);
                } else {
                    $text = trans('qualification.YouNeedToConfirm');
                    $passed = false;
                }
                break;
        }
        return [
            'text' => $text,
            'button' => $button,
            'passed' => $passed,
        ];
    }

    /**
     * Create a registration record given the user and the camp with an optional parameter, application status,
     * in case we know exactly the application status to set.
     *
     */
    public static function register(Camp $camp, User $user, $status = ApplicationStatus::DRAFT, bool $badge_check = false)
    {
        $ineligible_reason = $user->getIneligibleReasonForCamp($camp);
        if ($ineligible_reason)
            throw new \CampPASSException($ineligible_reason);
        $registration = $camp->getRegistration($user);
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
        // Notify all camp makers of this camp for this new application
        if ($status >= ApplicationStatus::APPLIED) {
            foreach ($camp->camp_makers() as $campmaker) {
                $campmaker->notify(new CamperStatusChanged($registration));
            }
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
        $json = QuestionManager::getQuestionJSON($camp->id);
        $json['answer'] = [];
        $json['answer_id'] = [];
        $pre_answers = $question_set->answers()->where('camper_id', $user->id)->get(['id', 'question_id', 'answer']);
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
        return self::submit_application_form($camp, $status = ApplicationStatus::CHOSEN);
    }

    public function store(Request $request)
    {
        $this->authenticate($camp = Camp::find($request['camp_id']));
        $user = auth()->user();
        if (!$user->can('answer-edit') || !$user->can('answer-create'))
            throw new \CampPASSExceptionPermission();
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
                    Storage::putFileAs("{$directory}/{$json_id}/{$user->id}", $file, "{$json_id}.pdf");
                }
            } else
                $answer_content = QuestionManager::encodeIfNeeded($request[$json_id], $question->type);
            if ($question->type == QuestionType::FILE && is_null($answer_content))
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
        $json = QuestionManager::getQuestionJSON($question_set->camp_id);
        $data = QuestionManager::getAnswers($question_set, $camper);
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
        return view('camp_application.done', compact('camp'));
    }

    public function consent_upload(StorePDFRequest $request, Registration $registration)
    {
        self::authenticate($registration->camp);
        self::authenticate_registration($registration);
        if (!$request->hasFile('pdf'))
            throw new \CampPASSExceptionNoFileUploaded();
        $directory = Common::consentDirectory($registration->camp_id);
        Storage::putFileAs($directory, $request->file('pdf'), "consent_{$registration->id}.pdf");
        return redirect()->back()->with('success', trans('registration.ConsentUploaded'));
    }

    public static function get_consent_path(Registration $registration)
    {
        $directory = Common::consentDirectory($registration->camp_id);
        $path = "{$directory}/consent_{$registration->id}.pdf";
        return Storage::exists($path) ? $path : null;
    }

    public function consent_download(Registration $registration)
    {
        self::authenticate($registration->camp);
        self::canAccessResource($registration->camper_id, $registration->camp_id);
        $path = $this->get_consent_path($registration);
        return Common::downloadFile($path);
    }

    public function consent_delete(Registration $registration)
    {
        self::authenticate($registration->camp);
        self::authenticate_registration($registration);
        $directory = Common::consentDirectory($registration->camp_id);
        $path = "{$directory}/consent_{$registration->id}.pdf";
        return Common::deleteFile($path);
    }

    public function payment_upload(StorePDFRequest $request, Registration $registration)
    {
        self::authenticate($registration->camp);
        self::authenticate_registration($registration);
        if (!$request->hasFile('pdf'))
            throw new \CampPASSExceptionNoFileUploaded();
        $directory = Common::paymentDirectory($registration->camp_id);
        Storage::putFileAs($directory, $request->file('pdf'), "payment_{$registration->id}.pdf");
        return redirect()->back()->with('success', trans('registration.PaymentUploaded'));
    }

    public static function get_payment_path(Registration $registration)
    {
        $directory = Common::paymentDirectory($registration->camp_id);
        $path = "{$directory}/payment_{$registration->id}.pdf";
        return Storage::exists($path) ? $path : null;
    }

    public function payment_download(Registration $registration)
    {
        self::authenticate($registration->camp);
        self::canAccessResource($registration->camper_id, $registration->camp_id);
        $path = $this->get_payment_path($registration);
        return Common::downloadFile($path);
    }

    public function payment_delete(Registration $registration)
    {
        self::authenticate($registration->camp);
        self::authenticate_registration($registration);
        $directory = Common::paymentDirectory($registration->camp_id);
        $path = "{$directory}/payment_{$registration->id}.pdf";
        return Common::deleteFile($path);
    }

    public static function status(Registration $registration)
    {
        self::authenticate($registration->camp);
        self::authenticate_registration($registration);
        return view('camp_application.status', compact('registration'));
    }

    public function unreturn(Registration $registration)
    {
        self::authenticate($registration->camp);
        self::authenticate_registration($registration);
        if (!$registration->returned)
            throw new \CampPASSExceptionRedirectBack();
        $registration->update([
            'returned' => false,
            'returned_reasons' => null,
        ]);
        return redirect()->back()->with('success', trans('registration.FormUnreturned'));
    }

    public static function confirm(Registration $registration, bool $silent = false)
    {
        $camp = $registration->camp;
        self::authenticate($camp, $silent = $silent);
        self::authenticate_registration($registration, $silent = $silent);
        // Campers who withdrawed from the camp and campers who are rejected from the camp and not the backups cannot confirm their attendance
        if ($registration->withdrawed() || ($registration->rejected() && !$camp->isCamperPassed($registration->camper)))
            throw new \CampPASSExceptionRedirectBack(trans('exception.YouAreNoLongerAbleToDoThat'));
        if ($registration->confirmed())
            throw new \CampPASSExceptionRedirectBack(trans('exception.AlreadyConfirmed', ['camp' => $camp]));
        if ($camp->canGetBackups()) {
            $form_score = $registration->form_score;
            $prevent = true;
            if ($form_score->backup) {
                $extended_confirmation_date = $confirmation_date->addDays(3);
                $prevent = Carbon::now()->diffInDays($extended_confirmation_date) < 0;
            }
            if ($prevent)
                throw new \CampPASSExceptionRedirectBack(trans('exception.YouAreNoLongerAbleToDoThat'));
        }
        if ($camp->hasPayment() && !self::get_payment_path($registration))
            throw new \CampPASSException();
        $camp_procedure = $camp->camp_procedure;
        // TODO: What about backups?
        if ($registration->status < ApplicationStatus::APPROVED && !$camp_procedure->walkIn() && !$camp_procedure->qaOnly())
            throw new \CampPASSExceptionRedirectBack(trans('exception.CannotConfirmUnapprovedForm'));
        $registration->update([
            'status' => ApplicationStatus::CONFIRMED,
        ]);
        BadgeController::addBadgeIfNeeded($registration);
        if (!$silent)
            return redirect()->back()->with('success', trans('qualification.FullyQualified', ['camp' => $camp]));
    }

    public static function withdraw(Registration $registration, bool $silent = false)
    {
        $camp = $registration->camp;
        self::authenticate($camp, $silent = $silent);
        self::authenticate_registration($registration, $silent = $silent);
        if ($registration->confirmed())
            throw new \CampPASSException(trans("exception.WithdrawAttendance"));
        if ($registration->withdrawed())
            throw new \CampPASSExceptionRedirectBack(trans('exception.AlreadyWithdrawed', ['camp' => $camp]));
        $registration->update([
            'status' => ApplicationStatus::WITHDRAWED,
            'returned' => false,
            'returned_reasons' => null,
        ]);
        // The corresponding registration record will be automatically marked as not passed, as they lost their chance to join the camp
        $form_score = $registration->form_score;
        if ($form_score) {
            $form_score->update([
                'passed' => false,
            ]);
        }
        // Notify the camp makers about this withdrawal
        foreach ($camp->camp_makers() as $campmaker) {
            $campmaker->notify(new CamperStatusChanged($registration));
        }
        if (!$silent)
            return redirect()->back()->with('message', trans('exception.WithdrawedFrom', ['camp' => $camp]));
    }

    public function canAccessAnswer(Answer $answer)
    {
        return $this->canAccessResource($answer->camper_id, $answer->question_set->camp_id);
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
        return Common::downloadFile($this->get_answer_file_path($answer));
    }

    /**
     * Delete the answer of type file.
     *
     */
    public function answer_file_delete(Answer $answer)
    {
        if (!auth()->user()->can('answer-delete'))
            throw new \CampPASSExceptionRedirectBack(trans('app.NoPermissionError'));
        $filepath = $this->get_answer_file_path($answer);
        $answer->delete();
        return Common::deleteFile($filepath);
    }
}
