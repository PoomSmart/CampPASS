<?php

namespace App\Console\Commands;

use App\Camp;
use App\Candidate;

use Carbon\Carbon;

use App\Enums\ApplicationStatus;

use App\Notifications\ApplicationStatusUpdated;

use Illuminate\Console\Command;

class BackupShift extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:shift {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically move the backups into the candidate list if there is such available space.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $camps = Camp::allApproved()->with('camp_procedure')->whereHas('camp_procedure', function ($query) {
            $query->where('candidate_required', true);
        })->get();
        $force = $this->option('force');
        foreach ($camps as $camp) {
            $question_set = $camp->question_set;
            if (!$question_set->total_score || !$camp->confirmation_date)
                continue;
            if (!$force && Carbon::now()->diffInDays(Carbon::parse($camp->confirmation_date)) >= 0)
                continue;
            $passed_candidates = $camp->candidates()->where('backup', false)->get();
            if ($passed_candidates->isEmpty())
                continue;
            $no_longer_passed = 0;
            logger()-> info(trans('camper.RejectingCampersNotConfirmedAttendance', ['camp' => $camp]));
            foreach ($passed_candidates as $passed_candidate) {
                $registration = $passed_candidate->registration;
                if (!$registration->rejected() && !$registration->confirmed()) {
                    $form_score = $passed_candidate->form_score;
                    $form_score->update([
                        'passed' => false,
                    ]);
                    $registration->update([
                        'status' => ApplicationStatus::REJECTED,
                        'returned' => false,
                        'returned_reasons' => null,
                        'remark' => null,
                    ]);
                    $camper = $passed_candidate->camper;
                    logger()->info(trans('camper.MakingCandidateRejected', ['camp' => $camp->getFullName()]));
                    $camper->notify(new ApplicationStatusUpdated($registration));
                    ++$no_longer_passed;
                }
            }
            if (!$no_longer_passed)
                continue;
            logger()->info(trans('camper.ShiftingEqualBackupsUp'));
            $candidates = $camp->candidates()->where('backup', true)->orderByDesc('total_score')->get();
            if ($camp->backup_limit)
                $candidates = $candidates->splice(0, min($no_longer_passed, $camp->backup_limit));
            foreach ($candidates as $candidate) {
                $candidate->form_score->makeBackupPassed();
                $registration = $candidate->registration;
                $registration->update([
                    'status' => ApplicationStatus::CHOSEN,
                ]);
                $camper = $candidate->camper;
                logger()->info(trans('camper.MakingBackUp', ['camper' => $camper->getFullName()]));
                $camper->notify(new ApplicationStatusUpdated($registration));
            }
        }
    }
}
