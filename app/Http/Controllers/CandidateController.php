<?php

namespace App\Http\Controllers;

use File;

use App\Camp;
use App\Common;
use App\Candidate;
use App\FormScore;
use App\Registration;
use App\QuestionSet;
use App\QuestionManager;
use App\User;

use App\Http\Controllers\CampApplicationController;
use App\Http\Controllers\QualificationController;

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
        $this->middleware('permission:candidate-list', ['only' => ['result', 'rank', 'announce', 'data_download_selection', 'data_download', 'interview_announce']]);
        $this->middleware('permission:candidate-edit', ['only' => ['interview_save', 'document_approve_save']]);
    }

    public static function document_approve(Registration $registration, $approved_by_id = null, bool $silent = false)
    {
        $camp = $registration->camp;
        // We will not approve this registration if the camper has not uploaded their payment slip for the camps that require deposit
        if ($camp->hasPayment() && !CampApplicationController::get_payment_path($registration))
            throw new \CampPASSExceptionRedirectBack();
        // Similarly, we reject those that have not uploaded a signed parental consent form
        if ($camp->parental_consent && !CampApplicationController::get_consent_path($registration))
            throw new \CampPASSExceptionRedirectBack();
        $registration->update([
            'status' => ApplicationStatus::APPROVED,
            'approved_by' => $approved_by_id ? $approved_by_id : auth()->user()->id,
        ]);
        $form_score = $registration->form_score;
        if ($form_score) {
            $form_score->update([
                'checked' => true,
            ]);
        }
    }

    public function document_approve_save(Request $request, Camp $camp)
    {
        $data = $request->all();
        unset($data['_token']);
        foreach ($camp->registrations as $registration) {
            try {
                if (isset($data[$registration->id])) {
                    $this->document_approve($registration, null, true);
                    $registration->camper->notify(new ApplicationStatusUpdated($registration));
                }
            } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', trans('qualification.DocumentsApproved'));
    }

    public static function interview_check_real(Registration $registration, string $checked)
    {
        if ($registration->withdrawed())
            return;
        $registration->update([
            'status' => strcmp($checked, 'true') == 0 ? ApplicationStatus::INTERVIEWED : ApplicationStatus::REJECTED,
        ]);
    }

    public function interview_save(Request $request, Camp $camp)
    {
        if ($camp->question_set->interview_announced)
            throw new \CampPASSExceptionRedirectBack();
        $data = $request->all();
        unset($data['_token']);
        $candidates = $camp->candidates()->where('backup', false)->get();
        foreach ($candidates as $candidate) {
            $registration = $candidate->registration;
            $this->interview_check_real($registration, isset($data[$registration->id]) ? 'true' : 'false');
        }
        return redirect()->back()->with('success', trans('qualification.InterviewedSaved'));
    }

    public static function interview_announce(QuestionSet $question_set, bool $silent = false)
    {
        if ($question_set->interview_announced)
            throw new \CampPASSExceptionRedirectBack();
        $candidates = $question_set->camp->candidates()->where('backup', false)->get();
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
        return $camp->candidates()->where('backup', false)->get()->filter(function ($candidate) {
            $registration = $candidate->registration;
            return !$registration->returned && $registration->chosen_to_confirmed();
        });
    }

    public function data_download(Request $request, QuestionSet $question_set)
    {
        if (sizeof($request->all()) <= 1)
            return redirect()->back();
        $camp = $question_set->camp;
        $download_path = public_path("{$camp}_data.zip");
        File::delete($download_path);
        $zipper = new Zipper;
        $make = $zipper->make($download_path);
        $root = storage_path('app').'/';
        if ($request->has('payment'))
            $make->folder('payment')->add(glob($root.Common::paymentDirectory($camp->id).'/*'));
        if ($request->has('consent-form'))
            $make->folder('consent-form')->add(glob($root.Common::consentDirectory($camp->id).'/*'));
        $candidates = $temp_dir = null;
        if ($request->has('submitted-form')) {
            $temp_dir = "{$root}camps/temp_{$question_set->id}";
            if (!File::exists($temp_dir))
                File::makeDirectory($temp_dir);
            $candidates = $this->candidates($camp);
            foreach ($candidates as $candidate) {
                // Try-catch for sanity
                try {
                    $temp_pdf_path = "{$temp_dir}/temp_{$candidate->registration_id}.pdf";
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
        $zipper->close();
        if ($temp_dir)
            File::deleteDirectory($temp_dir);
        unset($zipper);
        return response()->download($download_path);
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
        $confirmed = $withdrawed = 0;
        // Count the numbers for the confirmed and the withdrawed
        foreach ($candidates->get() as $candidate) {
            $registration = $candidate->registration;
            if ($registration->confirmed())
                ++$confirmed;
            else if ($registration->withdrawed())
                ++$withdrawed;
        }
        $summary = trans('qualification.TotalCandidates', [
            'total' => $total,
            'confirmed' => $confirmed,
            'not_confirmed' => $total - $confirmed - $withdrawed,
            'withdrawed' => $withdrawed,
        ]);
        $rank_by_score = $question_set->total_score;
        if ($rank_by_score) {
            // Backups only matter for the camps that have gradable question set
            $backup_confirmed = $backup_withdrawed = 0;
            $backups = $camp->candidates()->where('backup', true)->get()->sortByDesc(function ($candidate) use (&$backup_confirmed, &$backup_withdrawed) {
                $registration = $candidate->registration;
                if ($registration->confirmed())
                    ++$backup_confirmed;
                else if ($registration->withdrawed())
                    ++$backup_withdrawed;
                return $candidate->form_score->total_score;
            });
            $backup_total = $backups->count();
            $backup_summary = trans('qualification.TotalCandidates', [
                'total' => $backup_total,
                'confirmed' => $backup_confirmed,
                'not_confirmed' => $backup_total - $backup_confirmed - $backup_withdrawed,
                'withdrawed' => $backup_withdrawed,
            ]);
            $can_get_backups = $camp->canGetBackups();
        } else {
            $backups = null;
            $backup_summary = null;
        }
        $locale = app()->getLocale();
        $candidates = $candidates->leftJoin('registrations', 'registrations.id', '=', 'candidates.registration_id');
        if ($only_true_passed)
            $candidates = $candidates->where('registrations.status', ApplicationStatus::CONFIRMED);
        $candidates = $candidates->leftJoin('users', 'users.id', '=', 'registrations.camper_id')
                        ->orderByDesc('registrations.status') // "Group" by registration status
                        ->orderBy('registrations.returned') // Seperated by whether the form has been returned
                        ->orderBy("users.name_{$locale}"); // Sorted by name at last
        $candidates = $candidates->paginate(Common::maxPagination());
        return Common::withPagination(view('qualification.candidate_result', compact('candidates', 'question_set', 'camp', 'summary', 'backup_summary', 'backups', 'can_get_backups', 'only_true_passed')));
    }

    public static function create_form_scores(Camp $camp, QuestionSet $question_set, $registrations)
    {
        $form_scores = $camp->form_scores();
        if ($form_scores->doesntExist()) {
            $auto_gradable = !$question_set->manual_required;
            $data = [];
            foreach ($registrations as $registration) {
                $data[] = [
                    'registration_id' => $registration->id,
                    'question_set_id' => $question_set->id,
                    'finalized' => $auto_gradable,
                    'submission_time' => $registration->submission_time,
                ];
            }
            FormScore::insert($data);
            unset($data);
            // This is the first time the ranking occurs, allow auto-grading
            $question_set->update([
                'auto_ranked' => false,
            ]);
            $form_scores = $camp->form_scores(); // TODO: Do we need this?
        }
        return $form_scores;
    }

    public static function rank(QuestionSet $question_set, bool $list = false, bool $without_withdrawed = false, bool $without_returned = false, bool $check_consent_paid = false)
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
        if ($without_withdrawed)
            $registrations = $registrations->where('registrations.status', '!=', ApplicationStatus::WITHDRAWED);
        $total_registrations = $registrations->count();
        $auto_gradable = !$question_set->manual_required;
        $form_scores = self::create_form_scores($camp, $question_set, $registrations);
        $finalized = 0;
        $average_score = $total_withdrawed = $total_candidates = 0;
        $form_scores_get = $form_scores->get();
        if ($question_set->total_score) {
            $minimum_score = $question_set->minimum_score;
            foreach ($form_scores_get as $form_score) {
                $registration = $form_score->registration;
                $withdrawed = $registration->withdrawed();
                if ($withdrawed) {
                    ++$total_withdrawed;
                    continue;
                }
                if (is_null($form_score->total_score)) {
                    $form_score->update([
                        'total_score' => QualificationController::form_grade($registration_id = $registration->id, $question_set_id = $question_set->id, $silent = true),
                    ]);
                }
                $paid = $check_consent_paid && $camp->application_fee ? CampApplicationController::get_payment_path($registration) : true;
                $consent = $check_consent_paid && $camp->parental_consent ? CampApplicationController::get_consent_path($registration) : true;
                if (!$question_set->auto_ranked) {
                    $form_score->update([
                        'passed' => $form_score->total_score >= $minimum_score,
                    ]);
                }
                $form_score->update([
                    'passed' => $form_score->passed && $paid && $consent,
                    'finalized' => $form_score->finalized || $auto_gradable,
                ]);
                if ($form_score->passed)
                    ++$total_candidates;
                if ($form_score->finalized && $form_score->checked)
                    ++$finalized;
                $average_score += $form_score->total_score;
            }
            $form_scores = $form_scores->orderByDesc('total_score');
            // This question set is marked as auto-graded, so it won't auto-grade the same, allowing the camp makers to manually grade
            $question_set->update([
                'auto_ranked' => true,
            ]);
        } else {
            // We have to add `submission_time` attribute to form score to prevent this hacky buggy join clause
            $form_scores = $form_scores->orderBy('submission_time');
            foreach ($form_scores_get as $form_score) {
                $registration = $form_score->registration;
                $paid = $check_consent_paid && $camp->application_fee ? CampApplicationController::get_payment_path($registration) : true;
                $consent = $check_consent_paid && $camp->parental_consent ? CampApplicationController::get_consent_path($registration) : true;
                $withdrawed = $registration->withdrawed();
                $form_score->update([
                    'passed' => !$withdrawed,
                ]);
                if ($registration->returned) {
                    $form_score->update([
                        'checked' => false,
                    ]);
                } else if ($withdrawed)
                    ++$total_withdrawed;
                else if ($form_score->passed)
                    ++$total_candidates;
                if (!$withdrawed && $form_score->finalized && $form_score->checked)
                    ++$finalized;
            }
        }
        if (!$finalized)
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoFinalApplicationRank'));
        $count = $total_registrations - $total_withdrawed;
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
            if ($has_consent) {
                $form_scores_get = $form_scores_get->filter(function ($form_score) {
                    return !is_null(CampApplicationController::get_consent_path($form_score->registration));
                });
            }
            return $form_scores_get;
        }
        if ($question_set->total_score) {
            $average_score = number_format($average_score / $total_registrations, 2);
            $total_failed = $total_registrations - $total_candidates;
            $summary = trans('qualification.TotalPassedFailedAvgScore', [
                'total_registrations' => $total_registrations,
                'total_candidates' => $total_candidates,
                'total_withdrawed' => $total_withdrawed,
                'total_failed' => $total_failed,
                'average_score' => $average_score,
            ]);
        } else {
            $total_failed = $total_registrations - $total_candidates - $total_withdrawed;
            $summary = trans('qualification.TotalPassedFailed', [
                'total_registrations' => $total_registrations,
                'total_candidates' => $total_candidates,
                'total_withdrawed' => $total_withdrawed,
                'total_failed' => $total_failed,
            ]);
        }
        $form_scores = $form_scores->paginate(Common::maxPagination());
        View::share('has_payment', $has_payment);
        View::share('has_consent', $has_consent);
        View::share('return_reasons', QualificationController::form_returned_reasons($has_payment));
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
            $form_scores = $form_scores ? $form_scores : self::rank($question_set, $list = true, $without_withdrawed = true, $without_returned = true, $check_consent_paid = true);
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
}
