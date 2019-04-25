<?php

namespace App\Http\Controllers;

use File;

use App\Answer;
use App\Camp;
use App\Common;
use App\Candidate;
use App\FormScore;
use App\Registration;
use App\QuestionSet;
use App\QuestionManager;
use App\User;

use App\Http\Controllers\CampApplicationController;

use App\Enums\QuestionType;
use App\Enums\ApplicationStatus;

use App\Notifications\ApplicationStatusUpdated;

use Chumper\Zipper\Zipper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:camper-list');
        $this->middleware('permission:answer-grade', ['only' => [
            'form_grade', 'save_manual_grade', 'form_finalize',
        ]]);
        $this->middleware('permission:candidate-list', ['only' => [
            'show_profile_detailed', 'result', 'rank', 'announce', 'data_download_selection', 'data_download', 'interview_announce', 'candidate_rank', 'candidate_announce',
        ]]);
        $this->middleware('permission:candidate-edit', ['only' => [
            'document_approve_save', 'document_approve_interview_save', 'form_return', 'form_reject', 'form_pass_save',
        ]]);
    }

    public static function document_approve(Registration $registration, $approved_by_id = null, bool $silent = false, bool $force_approved = false)
    {
        $camp = $registration->camp;
        if ($force_approved || !$camp->application_fee) {
            // We will not approve this registration if the camper has not uploaded their payment slip for the camps that require deposit
            if ($camp->deposit && !CampApplicationController::get_payment_path($registration))
                throw new \CampPASSExceptionRedirectBack();
            $registration->update([
                'status' => ApplicationStatus::APPROVED,
                'approved_by' => $approved_by_id ? $approved_by_id : auth()->user()->id,
            ]);
        }
        $registration->form_score->update([
            'checked' => true,
        ]);
    }

    public function document_approve_save(Request $request, Camp $camp)
    {
        $data = $request->all();
        unset($data['_token']);
        if ($camp->camp_procedure->interview_required && !$camp->question_set->interview_announced)
            throw new \CampPASSExceptionRedirectBack();
        // TODO: Tell users that this will be irreversible ?
        foreach ($data as $registration_id => $value) {
            try {
                $registration = Registration::find($registration_id);
                $this->document_approve($registration, null, true);
                $registration->camper->notify(new ApplicationStatusUpdated($registration));
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', trans('qualification.DocumentsApproved'));
    }

    public static function interview_check_real(Registration $registration, string $checked)
    {
        if ($registration->withdrawn())
            return;
        $registration->update([
            'status' => strcmp($checked, 'true') == 0 ? ApplicationStatus::INTERVIEWED : ApplicationStatus::REJECTED,
        ]);
    }

    public function document_approve_interview_save(Request $request, Camp $camp)
    {
        $data = $request->all();
        unset($data['_token']);
        $interview_data = isset($data['interview']) ? $data['interview'] : null;
        if ($interview_data && !$camp->question_set->interview_announced) {
            $candidates = $camp->candidates->where('backup', false);
            foreach ($candidates as $candidate) {
                $registration = $candidate->registration;
                $this->interview_check_real($registration, isset($interview_data[$registration->id]) ? 'true' : 'false');
            }
        }
        $consent_data = isset($data['consent']) ? $data['consent'] : null;
        if ($consent_data && !$camp->camp_procedure->interview_required || $camp->question_set->interview_announced) {
            // TODO: Tell users that this will be irreversible ?
            foreach ($consent_data as $registration_id => $value) {
                try {
                    $registration = Registration::find($registration_id);
                    $this->document_approve($registration, null, true, true);
                    $registration->camper->notify(new ApplicationStatusUpdated($registration));
                } catch (\Exception $e) {}
            }
        }
        return redirect()->back()->with('success', trans('qualification.StatusSaved'));
    }

    public static function interview_announce(QuestionSet $question_set, bool $silent = false)
    {
        if ($question_set->interview_announced)
            throw new \CampPASSExceptionRedirectBack();
        $candidates = $question_set->camp->candidates->where('backup', false);
        foreach ($candidates as $candidate) {
            $registration = $candidate->registration;
            $interviewed = $registration->interviewed();
            self::interview_check_real($registration, $interviewed ? 'true' : 'false');
            if (!$interviewed)
                continue;
            $registration->camper->notify(new ApplicationStatusUpdated($registration));
        }
        $question_set->update([
            'interview_announced' => true,
        ]);
        if (!$silent)
            return redirect()->back()->with('success', trans('qualification.InterviewedAnnounced'));
    }

    private static function candidates(Camp $camp)
    {
        return $camp->candidates->where('backup', false)->filter(function ($candidate) {
            $registration = $candidate->registration;
            return !$registration->returned && $registration->chosen_to_confirmed();
        });
    }

    public function data_download(Request $request, QuestionSet $question_set)
    {
        if (sizeof($request->all()) <= 1)
            return redirect()->back();
        $camp = $question_set->camp;
        $download_path = public_path("camp_{$camp->id}_data.zip");
        File::delete($download_path);
        $zipper = new Zipper;
        $make = $zipper->make($download_path);
        $root = storage_path('app').'/';
        if ($request->has('payment'))
            $make->folder('payment')->add(glob($root.Common::paymentDirectory($camp->id).'/*'));
        if ($request->has('consent-form'))
            $make->folder('consent-form')->add(glob($root.Common::consentDirectory($camp->id).'/*'));
        $candidates = $temp_dir_form = $temp_dir_allergy = null;
        if ($request->has('submitted-form')) {
            $temp_dir_form = "{$root}camps/temp_{$question_set->id}";
            if (!File::exists($temp_dir_form))
                File::makeDirectory($temp_dir_form);
            $candidates = $this->candidates($camp);
            foreach ($candidates as $candidate) {
                // Try-catch for sanity
                try {
                    $temp_pdf_path = "{$temp_dir_form}/temp_{$candidate->registration_id}.pdf";
                    $user = $candidate->camper;
                    $json = QuestionManager::getQuestionJSON($question_set->camp_id);
                    $data = QuestionManager::getAnswers($question_set, $user);
                    \SnappyPDF::loadView('layouts.submitted_form', compact('user', 'data', 'json'))->save($temp_pdf_path, true);
                    $make->folder('submitted-form')->add($temp_pdf_path, "form_{$candidate->registration_id}.pdf");
                } catch (\Exception $e) {
                    logger()->debug($e);
                }
            }
        }
        foreach (['transcript', 'confirmation_letter'] as $folder) {
            if ($request->has($folder)) {
                if (is_null($candidates))
                    $candidates = $this->candidates($camp);
                foreach ($candidates as $candidate) {
                    $camper_id = $candidate->camper_id;
                    $path = $root.Common::userFileDirectory($camper_id)."/{$folder}.pdf";
                    $make->folder($folder)->add($path, "{$folder}_{$camper_id}.pdf");
                }
            }
        }
        if ($request->has('allergy')) {
            $temp_dir_allergy = "{$root}camps/temp_allergy";
            $temp_allergy_path = "{$temp_dir_allergy}/allergy.pdf";
            $allergy_list = [];
            if (is_null($candidates))
                $candidates = $this->candidates($camp);
            $allergic_candidates = $candidates->filter(function ($candidate) {
                return !is_null($candidate->camper->allergy);
            });
            foreach ($allergic_candidates as $candidate) {
                $camper = $candidate->camper;
                $allergy_list[$camper->getFullName()] = $camper->allergy;
            }
            \SnappyPDF::loadView('layouts.allergy_table', compact('allergy_list'))->save($temp_allergy_path, true);
            $make->folder('allergy')->add($temp_allergy_path, "allergy_list.pdf");
        }
        $zipper->close();
        if ($temp_dir_form)
            File::deleteDirectory($temp_dir_form);
        if ($temp_dir_allergy)
            File::deleteDirectory($temp_dir_allergy);
        unset($zipper);
        return response()->download($download_path)->deleteFileAfterSend();
    }

    public function data_download_selection(QuestionSet $question_set)
    {
        $camp = $question_set->camp;
        $camp_procedure = $camp->camp_procedure;
        return view('qualification.data_download_selection', compact('question_set', 'camp', 'camp_procedure'));
    }

    public function result(QuestionSet $question_set)
    {
        $camp = $question_set->camp;
        $candidates = $camp->candidates()->where('backup', false);
        // This can occur when the minimum score is too high and no one passed
        if ($candidates->doesntExist())
            throw new \CampPASSException(trans('exception.NoCandidateResultsToShow'));
        $only_true_passed = Input::get('only_true_passed', null);
        $can_get_backups = false;
        $total = $candidates->count();
        $confirmed = $withdrawn = 0;
        // Count the numbers for the confirmed and the withdrawn
        foreach ($candidates->get() as $candidate) {
            $registration = $candidate->registration;
            if ($registration->confirmed())
                ++$confirmed;
            else if ($registration->withdrawn())
                ++$withdrawn;
        }
        $summary = trans('qualification.TotalCandidates', [
            'total' => $total,
            'confirmed' => $confirmed,
            'not_confirmed' => $total - $confirmed - $withdrawn,
            'withdrawn' => $withdrawn,
        ]);
        $rank_by_score = $question_set->total_score;
        if ($rank_by_score) {
            // Backups only matter for the camps that have gradable question set
            $backup_confirmed = $backup_withdrawn = 0;
            $backups = $camp->candidates->where('backup', true)->sortByDesc(function ($candidate) use (&$backup_confirmed, &$backup_withdrawn) {
                $registration = $candidate->registration;
                if ($registration->confirmed())
                    ++$backup_confirmed;
                else if ($registration->withdrawn())
                    ++$backup_withdrawn;
                return $candidate->form_score->total_score;
            });
            $backup_total = $backups->count();
            $backup_summary = trans('qualification.TotalCandidates', [
                'total' => $backup_total,
                'confirmed' => $backup_confirmed,
                'not_confirmed' => $backup_total - $backup_confirmed - $backup_withdrawn,
                'withdrawn' => $backup_withdrawn,
            ]);
            $can_get_backups = $camp->canGetBackups();
        } else {
            $backups = null;
            $backup_summary = null;
        }
        $locale = app()->getLocale();
        $candidates = $candidates->leftJoin('registrations', 'registrations.id', '=', 'candidates.registration_id');
        if ($only_true_passed) // TODO: Check consent uploaded
            $candidates = $candidates->where('registrations.status', ApplicationStatus::CONFIRMED);
        $candidates = $candidates->leftJoin('users', 'users.id', '=', 'registrations.camper_id')
                        ->orderByDesc('registrations.status') // "Group" by registration status
                        ->orderBy('registrations.returned') // Seperated by whether the form has been returned
                        ->orderBy("users.name_{$locale}"); // Sorted by name at last
        $candidates = $candidates->paginate(Common::maxPagination());
        return Common::withPagination(view('qualification.candidate_result', compact('candidates', 'question_set', 'camp', 'summary', 'backup_summary', 'backups', 'can_get_backups', 'only_true_passed')));
    }

    public static function create_form_scores(Camp $camp, ?QuestionSet $question_set, $registrations)
    {
        $form_scores = $camp->form_scores();
        $auto_gradable = $question_set ? !$question_set->manual_required : false;
        if ($form_scores->doesntExist()) {
            $data = [];
            foreach ($registrations as $registration) {
                $data[] = [
                    'registration_id' => $registration->id,
                    'question_set_id' => $question_set ? $question_set->id : null,
                    'camp_id' => $camp->id,
                    'finalized' => $auto_gradable,
                    'submission_time' => $registration->submission_time,
                ];
            }
            FormScore::insert($data);
            unset($data);
            if ($question_set) {
                // This is the first time the ranking occurs, allow auto-grading
                $question_set->update([
                    'auto_ranked' => false,
                ]);
            }
        } else {
            // For other registration records that may be later added, create form scores for them
            foreach ($registrations as $registration) {
                if (is_null($registration->form_score)) {
                    FormScore::create([
                        'registration_id' => $registration->id,
                        'question_set_id' => $question_set->id,
                        'camp_id' => $camp->id,
                        'finalized' => $auto_gradable,
                        'submission_time' => $registration->submission_time,
                    ]);
                }
            }
        }
        return $form_scores;
    }

    public static function rank(QuestionSet $question_set, bool $list = false, bool $without_withdrawn = false, bool $without_returned = false, bool $check_consent_paid = false)
    {
        if (!$question_set->finalized)
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoApplicationRank'));
        if ($question_set->candidate_announced)
            throw new \CampPASSExceptionRedirectBack(trans('qualification.CandidatesAnnounced'));
        $camp = $question_set->camp;
        // We shouldn't be able to rank the forms that have nothing to do with scoring
        if (!$camp->camp_procedure->candidate_required)
            throw new \CampPASSException();
        $registrations = $camp->registrations;
        if ($registrations->isEmpty())
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoApplicationRank'));
        if ($without_returned)
            $registrations = $registrations->where('registrations.returned', false);
        if ($without_withdrawn)
            $registrations = $registrations->where('registrations.status', '!=', ApplicationStatus::WITHDRAWN);
        $total_registrations = $registrations->count();
        $auto_gradable = !$question_set->manual_required;
        $form_scores = self::create_form_scores($camp, $question_set, $registrations);
        $finalized = $average_score = $total_withdrawn = $total_rejected = $total_candidates = 0;
        $form_scores_get = $form_scores->get();
        $form_scores = $form_scores->leftJoin('registrations', 'registrations.id', '=', 'form_scores.registration_id')
                        ->orderByDesc('registrations.status') // "Group" by registration status
                        ->orderBy('registrations.returned'); // Seperated by whether the form has been returned
        $rank_by_score = $question_set->total_score;
        if (!$rank_by_score)
            $form_scores = $form_scores->orderBy('submission_time');
        $minimum_score = $rank_by_score ? $question_set->minimum_score : 0;
        foreach ($form_scores_get as $form_score) {
            $registration = $form_score->registration;
            $withdrawn = $registration->withdrawn();
            $rejected = $registration->rejected();
            if ($withdrawn)
                ++$total_withdrawn;
            else if ($rejected)
                ++$total_rejected;
            if ($rank_by_score && is_null($form_score->total_score)) {
                $form_score->update([
                    'total_score' => self::form_grade($registration_id = $registration->id, $question_set_id = $question_set->id, $silent = true),
                ]);
            }
            $paid = $check_consent_paid && $camp->application_fee ? CampApplicationController::get_payment_path($registration) : true;
            // $consent = $check_consent_paid && $camp->parental_consent ? CampApplicationController::get_consent_path($registration) : true;
            if ($rank_by_score && !$question_set->auto_ranked) {
                $form_score->update([
                    'passed' => $form_score->total_score >= $minimum_score,
                ]);
            }
            $form_score->update([
                'passed' => $form_score->passed && !$withdrawn && $paid,
                'finalized' => $form_score->finalized || $auto_gradable,
            ]);
            if ($form_score->passed)
                ++$total_candidates;
            if ($form_score->finalized && $form_score->checked)
                ++$finalized;
            $average_score += $form_score->total_score;
        }
        if ($rank_by_score) {
            $form_scores = $form_scores->orderByDesc('total_score');
            // This question set is marked as auto-graded, so it won't auto-grade again, allowing the camp makers to manually grade
            $question_set->update([
                'auto_ranked' => true,
            ]);
        }
        if (!$finalized)
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoFinalApplicationRank'));
        $count = $total_registrations - $total_withdrawn - $total_rejected;
        if ($finalized !== $count)
            throw new \CampPASSExceptionRedirectBack(trans('exception.AllApplicationFinalRank')." ({$finalized} vs {$count})");
        $has_payment = $camp->paymentOnly() ? true : $question_set && $question_set->candidate_announced && $camp_procedure->deposit_required;
        $has_consent = $camp->parental_consent;
        if ($list) {
            if ($has_payment) {
                $form_scores_get = $form_scores_get->filter(function ($form_score) {
                    return !is_null(CampApplicationController::get_payment_path($form_score->registration));
                });
            }
            /*if ($has_consent) {
                $form_scores_get = $form_scores_get->filter(function ($form_score) {
                    return !is_null(CampApplicationController::get_consent_path($form_score->registration));
                });
            }*/
            return $form_scores_get;
        }
        if ($rank_by_score) {
            $average_score = number_format($average_score / $total_registrations, 2);
            $total_failed = $total_registrations - $total_candidates;
            $summary = trans('qualification.TotalPassedFailedAvgScore', [
                'total_registrations' => $total_registrations,
                'total_candidates' => $total_candidates,
                'total_withdrawn' => $total_withdrawn,
                'total_failed' => $total_failed,
                'average_score' => $average_score,
            ]);
        } else {
            $total_failed = $total_registrations - $total_candidates - $total_withdrawn;
            $summary = trans('qualification.TotalPassedFailed', [
                'total_registrations' => $total_registrations,
                'total_candidates' => $total_candidates,
                'total_withdrawn' => $total_withdrawn,
                'total_failed' => $total_failed,
            ]);
        }
        $form_scores = $form_scores->paginate(Common::maxPagination());
        View::share('has_payment', $has_payment);
        View::share('has_consent', $has_consent);
        View::share('return_reasons', self::form_returned_reasons($has_payment));
        return Common::withPagination(view('qualification.candidate_rank', compact('form_scores', 'question_set', 'camp', 'summary')));
    }

    public static function announce(QuestionSet $question_set, bool $silent = false, $form_scores = null)
    {
        if (!$question_set->finalized)
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoApplicationRank'));
        if ($question_set->candidate_announced)
            throw new \CampPASSExceptionRedirectBack(trans('qualification.CandidatesAnnounced'));
        // The qualified campers are those that have form score checked and passing the minimum score
        $no_passed = $no_checked = 0;
        try {
            $form_scores = $form_scores ? $form_scores : self::rank($question_set, $list = true, $without_withdrawn = true, $without_returned = true, $check_consent_paid = true);
            $form_scores->each(function ($form_score) use (&$question_set, &$no_passed, &$no_checked) {
                if ($form_score->passed) {
                    ++$no_passed;
                    if ($form_score->checked)
                        ++$no_checked;
                }
            });
        } catch (\Exception $e) {
            throw $e;
        }
        if (!$no_passed)
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoCamperAnnounced'));
        if (!$silent && $no_passed != $no_checked)
            throw new \CampPASSExceptionRedirectBack(trans('exception.AllPassedFormsMustBeChecked'));
        $candidates = [];
        $camp = $question_set->camp;
        $camp_procedure = $camp->camp_procedure;
        $backup_count = 0;
        foreach ($form_scores as $form_score) {
            $backup = false;
            $registration = $form_score->registration;
            if ($form_score->passed) {
                // The application form can be approved now if they do not need to pay anything and have an interview
                $next_status = !$camp->hasPayment() && !$camp_procedure->interview_required ? ApplicationStatus::APPROVED : ApplicationStatus::CHOSEN;
                $registration->update([
                    'status' => $next_status,
                ]);
            } else {
                // Otherwise, the application form will be rejected
                $registration->update([
                    'status' => ApplicationStatus::REJECTED,
                    'returned' => false,
                    'returned_reasons' => null,
                    'remark' => null,
                ]);
                if (++$backup_count <= $camp->backup_limit) {
                    $form_score->update([
                        'passed' => true,
                    ]);
                    $backup = true;
                }
            }
            if ($form_score->passed) {
                $camper = $registration->camper;
                // Let campers know their application status
                if (!$backup)
                    $camper->notify(new ApplicationStatusUpdated($registration));
                $candidates[] = [
                    'camper_id' => $camper->id,
                    'camp_id' => $camp->id,
                    'registration_id' => $form_score->registration_id,
                    'form_score_id' => $form_score->id,
                    'total_score' => $form_score->total_score,
                    'backup' => $backup,
                ];
            }
        }
        Candidate::insert($candidates);
        unset($candidates);
        // Candidates are finalized, this question set will no longer be editable
        $question_set->update([
            'candidate_announced' => true,
        ]);
        if (!$silent)
            return redirect()->route('qualification.candidate_result', $question_set->id)->with('success', trans('qualification.CandidatesAnnounced'));
    }

    public static function form_reject(Registration $registration)
    {
        if ($registration->rejected())
            throw new \CampPASSExceptionRedirectBack();
        $registration->update([
            'status' => ApplicationStatus::REJECTED,
            'returned' => false,
            'returned_reasons' => null,
            'remark' => null,
        ]);
        $registration->form_score->update([
            'passed' => false,
        ]);
        return redirect()->back()->with('message', trans('qualification.ApplicantRejected', [
            'applicant' => $registration->camper->getFullName(),
        ]));
    }

    /**
     * Grade an application form from a camper (represented by a registration record) and the respective question set
     *
     */
    public static function form_grade($registration_id, $question_set_id, bool $silent = false)
    {
        $registration = Registration::findOrFail($registration_id);
        $form_score = $registration->form_score;
        if ($silent && isset($form_score->total_score))
            return $form_score->total_score;
        Common::authenticate_camp($registration->camp);
        if ($registration->unsubmitted())
            throw new \CampPASSException(trans('exception.CannotGradeUnsubmittedForm'));
        $camper = $registration->camper;
        $question_set = QuestionSet::findOrFail($question_set_id);
        $answers = $question_set->answers()->where('camper_id', $camper->id);
        if ($answers->doesntExist()) // This should not happen
            throw new \CampPASSException(trans('exception.CannotGradeFormWithoutQuestions'));
        $answers = $answers->get();
        $data = [];
        $json = QuestionManager::getQuestionJSON($question_set->camp_id, $encode = false, $graded = true);
        if (!$silent) {
            $json['question_scored'] = [];
            $json['question_lock'] = [];
            $json['question_full_score'] = [];
        }
        $auto_gradable_score = 0;
        $total_auto_gradable_score = 0;
        $camper_score = 0;
        $total_score = 0;
        foreach ($answers as $answer) {
            $question = $answer->question;
            if ($form_score->finalized)
                $json['question_lock'][$question->json_id] = 1;
            $answer_score = $answer->score;
            $answer_value = $answer->answer;
            // Grade the questions that need to be graded and are of choice type
            if (isset($json['question_graded'][$question->json_id])) {
                if ($question->type == QuestionType::CHOICES) {
                    $solution = $json['radio'][$question->json_id];
                    $score = $solution == $answer_value ? $question->full_score : 0;
                    if (!$silent) {
                        $json['question_scored'][$question->json_id] = $score;
                        $json['question_lock'][$question->json_id] = 1;
                    }
                    $auto_gradable_score += $score;
                    $camper_score += $score;
                    if (!isset($answer_score)) {
                        $answer->update([
                            'score' => $score,
                        ]);
                    }
                    $total_auto_gradable_score += $question->full_score;
                } else if (isset($answer_score)) {
                    // If the type is not choice, camp makers have graded it and the score has been saved to the database, so we send this information to the view
                    $json['question_scored'][$question->json_id] = $answer_score;
                    $camper_score += $answer_score;
                }
                $total_score += $question->full_score;
            }
            if (!$silent) {
                $json['question_full_score'][$question->json_id] = $question->full_score;
                $data[] = [
                    'question' => $question,
                    'answer' => QuestionManager::decodeIfNeeded($answer_value, $question->type),
                ];
            }
        }
        if (!$form_score) {
            $form_score = FormScore::create([
                'registration_id' => $registration_id,
                'question_set_id' => $question_set_id,
                'total_score' => $camper_score,
                'finalized' => !$question_set->manual_required, // If there are no gradable questions, the form is finalized and can be ranked
                'submission_time' => $registration->submission_time,
            ]);
        }
        $form_score->update([
            'total_score' => $camper_score,
        ]);
        if ($silent)
            return $camper_score;
        if ($total_score) {
            if ($question_set->manual_required)
                $score_report = trans('qualification.FormSummary', [
                    'camper_score' => $camper_score,
                    'total_score' => $total_score,
                ]);
            else
                $score_report = $total_auto_gradable_score ? trans('qualification.FormSummaryAuto', [
                    'auto_gradable' => $auto_gradable_score,
                    'total_auto_gradable' => $total_auto_gradable_score,
                    'camper_score' => $camper_score,
                    'total_score' => $total_score,
                ]) : null;
        } else
            $score_report = null;
        return view('qualification.form_grade', compact('camper', 'data', 'json', 'score_report', 'form_score'));
    }

    public function save_manual_grade(Request $request, Registration $registration, $question_set_id)
    {
        Common::authenticate_camp($registration->camp);
        $form_score = $registration->form_score;
        if ($form_score->finalized)
            throw new \CampPASSExceptionRedirectBack(trans('exception.CannotUpdateFinalizedForm'));
        $form_data = $request->all();
        // We don't need token
        unset($form_data['_token']);
        $camper = $registration->camper;
        $question_set = QuestionSet::findOrFail($question_set_id);
        $answers = $question_set->answers()->where('camper_id', $camper->id);
        if ($answers->doesntExist())
            throw new \CampPASSException(trans('exception.NoAnswersSaved'));
        // For all answers given, update all scores of those that will be manually graded
        $answers = $answers->get();
        foreach ($form_data as $id => $value) {
            if (substr($id, 0, 13) === 'manual_score_') {
                $key = substr($id, 13);
                $answer = $answers->filter(function ($answer) use (&$key) {
                    return $answer->question->json_id == $key;
                })->first();
                if (!$answer) {
                    logger()->error('Trying to parse an answer that does not exist.');
                    continue;
                }
                $answer->update([
                    'score' => (double)$value,
                ]);
            }
        }
        return redirect()->back()->with('success', trans('qualification.ScoresUpdated'));
    }

    public static function form_returned_reasons(bool $has_payment = true)
    {
        return [
            'document' => trans('qualification.StudentDocumentIssue'),
            'profile' => trans('qualification.ProfileIssue'),
        ] + ($has_payment ? [
            'payment' => trans('qualification.PaymentSlipIssue'),
        ] : []);
    }

    public function show_profile_detailed(Registration $registration)
    {
        View::share('fields_disabled', true);
        return ProfileController::edit($registration->camper, $me = false);
    }

    public function form_return(Request $request, Registration $registration)
    {
        $this->validate($request, [
            'reasons' => 'min:1',
            'reasons.*' => 'in:payment,document,profile',
            'remark' => 'nullable|string|max:300',
        ]);
        if ($registration->approved())
            throw new \CampPASSExceptionRedirectBack();
        $reasons = $request->reasons;
        $registration->form_score->update([
            'checked' => false,
        ]);
        $registration->update([
            'returned' => true,
            'returned_reasons' => json_encode($reasons, JSON_UNESCAPED_UNICODE),
            'remark' => $request->remark,
        ]);
        $candidate = $registration->camper;
        $candidate->notify(new ApplicationStatusUpdated($registration));
        return redirect()->back()->with('message', trans('qualification.FormReturned', [ 'candidate' => $candidate->getFullName() ]));
    }

    public static function form_finalize(FormScore $form_score, bool $silent = false)
    {
        $camp = $form_score->question_set->camp;
        Common::authenticate_camp($camp);
        if (!$form_score->finalized) {
            if ($form_score->registration->unsubmitted())
                throw new \CampPASSExceptionRedirectBack(trans('exception.CannotFinalizeUnsubmittedForm'));
            $form_score->update([
                'finalized' => true,
            ]);
        }
        if (!$silent)
            return redirect()->route('camps.registration', $camp->id)->with('success', trans('qualification.FormFinalized', [ 'candidate' => $form_score->registration->camper->getFullName() ]));
    }

    public static function form_pass_real(FormScore $form_score, $checked)
    {
        Common::authenticate_camp($form_score->question_set->camp);
        if ($form_score->registration->withdrawn())
            throw new \CampPASSExceptionRedirectBack();
        $form_score->update([
            'passed' => $checked == 'true',
        ]);
    }

    public function form_pass_save(Request $request, Camp $camp)
    {
        $data = $request->all();
        unset($data['_token']);
        foreach ($camp->form_scores()->get() as $form_score) {
            $registration = $form_score->registration;
            if ($registration->rejected() || $registration->withdrawn())
                continue;
            try {
                $this->form_pass_real($form_score, isset($data[$registration->id]) ? 'true' : 'false');
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', trans('qualification.FormsPassedSaved'));
    }
}