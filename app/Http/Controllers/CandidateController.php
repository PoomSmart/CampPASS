<?php

namespace App\Http\Controllers;

use App\Common;
use App\Candidate;
use App\FormScore;
use App\QuestionSet;

use App\Http\Controllers\QualificationController;

use App\Enums\ApplicationStatus;

use App\Notifications\ApplicationStatusUpdated;

use Illuminate\Http\Request;

class CandidateController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:camper-list');
        $this->middleware('permission:candidate-list', ['only' => ['result', 'rank', 'announce', 'data_export', 'data_download']]);
    }

    public function data_download(Request $request, QuestionSet $question_set)
    {
        $result = $this->result($question_set, $export = true);
        return $result;
    }

    public function data_export_selection(QuestionSet $question_set)
    {
        $camp = $question_set->camp;
        $camp_procedure = $camp->camp_procedure;
        return view('qualification.data_export_selection', compact('question_set', 'camp', 'camp_procedure'));
    }

    public function result(QuestionSet $question_set, bool $export = false)
    {
        $camp = $question_set->camp;
        $candidates = $camp->candidates()->where('backup', false);
        if ($candidates->doesntExist()) {
            if ($export) return null;
            throw new \CampPASSException(trans('exception.NoCandidateResultsToShow'));
        }
        $backups = null;
        if (!$export) {
            $total = $candidates->count();
            $confirmed = $withdrawed = 0;
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
            } else
                $backup_summary = null;
        }
        $locale = app()->getLocale();
        $candidates = $candidates->leftJoin('users', 'users.id', '=', 'candidates.camper_id')->orderBy("users.name_{$locale}");
        if ($export) {
            return [
                'candidates' => $candidates->get(),
                'backups' => $backups,
            ];
        }
        $candidates = $candidates->paginate(Common::maxPagination());
        return Common::withPagination(view('qualification.candidate_result', compact('candidates', 'question_set', 'camp', 'summary', 'backup_summary', 'backups')));
    }

    public static function rank(QuestionSet $question_set, bool $list = false, bool $with_withdrawed = true, bool $with_returned = true)
    {
        if (!$question_set->finalized)
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoApplicationRank'));
        if ($question_set->announced)
            throw new \CampPASSExceptionRedirectBack(trans('exception.CandidatesAnnounced'));
        $form_scores = FormScore::where('question_set_id', $question_set->id);
        if ($form_scores->doesntExist()) {
            if ($list) return null;
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoApplicationRank'));
        }
        $form_scores = $form_scores->with('registration')->whereHas('registration', function ($query) use (&$with_withdrawed, &$with_returned) {
            // These unsubmitted forms by common sense should be rejected from the grading process at all
            if (!$with_returned)
                $query->where('registrations.returned', false);
            $query->where('registrations.status', ApplicationStatus::APPLIED)->orWhere('registrations.status', ApplicationStatus::APPROVED)->orWhere('registrations.status', ApplicationStatus::CHOSEN);
            if ($with_withdrawed)
                $query->orWhere('registrations.status', ApplicationStatus::WITHDRAWED);
        });
        $total_registrations = $form_scores->count();
        if ($question_set->manual_required)
            $form_scores = $form_scores->where('finalized', true); // We would not grade unfinalized answers
        else {
            // If the question set can be entirely automatically graded, we say it is finalized
            $form_scores->update([
                'finalized' => true,
            ]);
        }
        if ($form_scores->doesntExist()) {
            if ($list) return null;
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoFinalApplicationRank'));
        }
        if ($form_scores->count() !== $total_registrations) {
            if ($list) return null;
            throw new \CampPASSExceptionRedirectBack(trans('exception.AllApplicationFinalRank'));
        }
        $average_score = $total_withdrawed = $total_candidates = 0;
        if ($question_set->total_score) {
            $minimum_score = $question_set->total_score * $question_set->score_threshold;
            foreach ($form_scores->get() as $form_score) {
                $withdrawed = $form_score->registration->withdrawed();
                if ($withdrawed) {
                    ++$total_withdrawed;
                    continue;
                }
                if (is_null($form_score->total_score)) {
                    $form_score->update([
                        'total_score' => QualificationController::form_grade($registration_id = $form_score->registration_id, $question_set_id = $question_set->id, $silent = true),
                    ]);
                }
                if (!$question_set->auto_ranked) {
                    $form_score->update([
                        'passed' => $form_score->total_score >= $minimum_score && ++$total_candidates,
                    ]);
                }
                $average_score += $form_score->total_score;
            }
            $form_scores = $form_scores->orderByDesc('total_score');
            $form_scores_get = $form_scores->get();
        } else {
            // TODO: We have to add `submission_time` attribute to form score to prevent this hacky buggy join clause
            //$form_scores = $form_scores->leftJoin('registrations', 'registrations.id', '=', 'form_scores.registration_id')
                //->orderByDesc('registrations.status')->orderBy('registrations.submission_time');
            $form_scores = $form_scores->orderBy('submission_time');
            $form_scores_get = $form_scores->get();
            foreach ($form_scores_get as $form_score) {
                $registration = $form_score->registration;
                $withdrawed = $registration->withdrawed();
                if (!$question_set->auto_ranked) {
                    $form_score->update([
                        'passed' => !$withdrawed,
                    ]);
                }
                if ($registration->returned) {
                    $form_score->update([
                        'checked' => false,
                    ]);
                } else if ($withdrawed) {
                    $form_score->update([
                        'checked' => true,
                    ]);
                    ++$total_withdrawed;
                } else if ($form_score->passed)
                    ++$total_candidates;
            }
        }
        // This question set is marked as auto-graded, so it won't auto-grade the same, allowing the camp makers to manually grade
        $question_set->update([
            'auto_ranked' => true,
        ]);
        if ($list)
            return $form_scores_get;
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
        $camp = $question_set->camp;
        $form_scores = $form_scores->paginate(Common::maxPagination());
        return Common::withPagination(view('qualification.candidate_rank', compact('form_scores', 'question_set', 'camp', 'summary')));
    }

    public static function announce(QuestionSet $question_set, bool $void = false, $form_scores = null)
    {
        if (!$question_set->finalized)
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoApplicationRank'));
        if ($question_set->announced)
            throw new \CampPASSExceptionRedirectBack(trans('exception.CandidatesAnnounced'));
        // The qualified campers are those that have form score checked and passing the threshold
        $no_passed = $no_checked = 0;
        $form_scores = $form_scores ? $form_scores : self::rank($question_set, $list = true, $with_withdrawed = false, $with_returned = false);
        if ($form_scores) {
            $form_scores->each(function ($form_score) use (&$question_set, &$no_passed, &$no_checked) {
                if ($form_score->passed) {
                    ++$no_passed;
                    if ($form_score->checked)
                        ++$no_checked;
                }
            });
        }
        if (!$no_passed)
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoCamperAnnounced'));
        if (!$void && $no_passed != $no_checked)
            throw new \CampPASSExceptionRedirectBack(trans('exception.AllPassedFormsMustBeChecked'));
        $candidates = [];
        $camp = $question_set->camp;
        $camp_procedure = $camp->camp_procedure;
        $backup_count = 0;
        foreach ($form_scores as $form_score) {
            $backup = false;
            $registration = $form_score->registration;
            if ($form_score->passed) {
                // The application form can be approved now if they do not need to pay the deposit
                $registration->update([
                    'status' => $camp_procedure->deposit_required ? ApplicationStatus::CHOSEN : ApplicationStatus::APPROVED,
                ]);
            } else {
                // Otherwise, the application form will be rejected
                $registration->update([
                    'status' => ApplicationStatus::REJECTED,
                ]);
                if (++$backup_count <= $camp->backup_limit)
                    $backup = true;
            }
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
        Candidate::insert($candidates);
        unset($candidates);
        // Candidates are finalized, this question set will no longer be editable
        $question_set->update([
            'announced' => true,
        ]);
        if (!$void)
            return redirect()->route('qualification.candidate_result', $question_set->id)->with('success', trans('qualification.CandidatesAnnounced'));
    }
}
