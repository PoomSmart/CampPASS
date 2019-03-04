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
        $this->middleware('permission:candidate-list', ['only' => ['result', 'rank', 'announce']]);
    }

    public function result(QuestionSet $question_set)
    {
        $form_scores = FormScore::with('registration')->where('question_set_id', $question_set->id)->where('finalized', true)->whereHas('registration', function ($query) {
           $query->where('status', '>=', ApplicationStatus::APPROVED);
        });
        if ($form_scores->doesntExist())
            throw new \CampPASSException(trans('exception.NoCandidateResultsToShow'));
        $total = $form_scores->count();
        $summary = "Total: {$total}";
        $camp = $question_set->camp;
        $form_scores = $form_scores->paginate(Common::maxPagination());
        return Common::withPagination(view('qualification.candidate_result', compact('form_scores', 'question_set', 'camp', 'summary')));
    }

    public static function rank(QuestionSet $question_set, bool $list = false)
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
        $form_scores = $form_scores->with('registration')->whereHas('registration', function ($query) {
             // These unsubmitted forms by common sense should be rejected from the grading process at all
            $query->where('registrations.status', ApplicationStatus::APPLIED)->orWhere('registrations.status', ApplicationStatus::CHOSEN);
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
        $minimum_score = $question_set->total_score * $question_set->score_threshold;
        if ($question_set->announced)
            $form_scores = $form_scores->where('total_score', '>=', $minimum_score);
        $average_score = 0;
        $total_candidates = 0;
        $form_scores_get = $form_scores->get();
        foreach ($form_scores_get as $form_score) {
            if (is_null($form_score->total_score)) {
                $form_score->update([
                    'total_score' => QualificationController::form_grade($registration_id = $form_score->registration_id, $question_set_id = $question_set->id, $silent = true),
                ]);
            }
            if ($form_score->total_score >= $minimum_score)
                ++$total_candidates;
            $average_score += $form_score->total_score;
        }
        $form_scores = $form_scores->orderByDesc('total_score');
        if ($list)
            return $form_scores_get;
        $average_score /= $total_registrations;
        $total_failed = $total_registrations - $total_candidates;
        $summary = trans('qualification.TotalPassedFailedAvgScore', [
            'total_registrations' => $total_registrations,
            'total_candidates' => $total_candidates,
            'total_failed' => $total_failed,
            'average_score' => $average_score
        ]);
        $camp = $question_set->camp;
        $form_scores = $form_scores->paginate(Common::maxPagination());
        return Common::withPagination(view('qualification.candidate_rank', compact('form_scores', 'question_set', 'camp', 'summary')));
    }

    public static function announce(QuestionSet $question_set, bool $void = false)
    {
        if (!$question_set->finalized)
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoApplicationRank'));
        if ($question_set->announced)
            throw new \CampPASSExceptionRedirectBack(trans('exception.CandidatesAnnounced'));
        // The qualified campers are those that have form score passing the criteria
        $no_passed = 0;
        $form_scores = self::rank($question_set, $list = true);
        if ($form_scores) {
            $form_scores->each(function ($form_score) use (&$question_set, &$no_passed) {
                if ($form_score->total_score / $question_set->total_score >= $question_set->score_threshold)
                    ++$no_passed;
            });
        }
        if (!$no_passed)
            throw new \CampPASSExceptionRedirectBack(trans('exception.NoCamperAnnounced'));
        $candidates = [];
        $camp_procedure = $question_set->camp->camp_procedure;
        foreach ($form_scores as $form_score) {
            $registration = $form_score->registration;
            $passed = $form_score->total_score / $question_set->total_score >= $question_set->score_threshold;
            if ($passed) {
                // The application form can be approved now if they do not need to pay the deposit
                $registration->update([
                    'status' => $camp_procedure->deposit_required ? ApplicationStatus::CHOSEN : ApplicationStatus::APPROVED,
                ]);
            } else {
                // Otherwise, the application form will be rejected
                $registration->update([
                    'status' => ApplicationStatus::REJECTED,
                ]);
            }
            // Let campers know their application status
            $registration->camper->notify(new ApplicationStatusUpdated($registration));
            $candidates[] = [
                'registration_id' => $form_score->registration_id,
                'total_score' => $form_score->total_score,
            ];
        }
        Candidate::insert($candidates);
        unset($candidates);
        // Candidates are finalized, this question set will no longer be editable
        $question_set->update([
            'announced' => true,
        ]);
        if (!$void)
            return redirect()->route('qualification.candidate_result', $question_set->id)->with('success', 'Candidates are announced.');
    }
}
