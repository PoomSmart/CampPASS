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
    protected $signature = 'backup:shift';

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
        // TODO: Test this
        $camps = Camp::allApproved()->with('camp_procedure')->whereHas('camp_procedure', function ($query) {
            $query->where('candidate_required', true);
        })->get();
        foreach ($camps as $camp) {
            $question_set = $camp->question_set;
            if (!$question_set->total_score || !$camp->confirmation_date)
                continue;
            if (Carbon::now()->diffInDays(Carbon::parse($camp->confirmation_date)) >= 0)
                continue;
            logger()->info("Rejecting all the passed campers who have not confirmed their attendance for the camp {$camp}");
            $passed_form_scores = $camp->getFormScores()->where('passed', true);
            $no_longer_passed = 0;
            foreach ($passed_form_scores as $passed_form_score) {
                $registration = $passed_form_score->registration;
                if (!$registration->confirmed()) {
                    $passed_form_score->update([
                        'passed' => false,
                    ]);
                    $registration->update([
                        'status' => ApplicationStatus::REJECTED,
                    ]);
                    ++$no_longer_passed;
                }
            }
            logger()->info("Shifting the equal amount of backups up");
            $form_scores = $camp->getFormScores()->where('backup', true)->orderByDesc('total_score');
            if ($camp->backup_limit)
                $form_scores = $form_scores->limit(min($no_longer_passed, $camp->backup_limit));
            $candidates = [];
            foreach ($form_scores as $form_score) {
                $form_score->makeBackupPassed();
                $registration = $form_score->registration;
                $registration->update([
                    'status' => ApplicationStatus::CHOSEN,
                ]);
                $form_score->camper->notify(new ApplicationStatusUpdated($registration));
                $candidates[] = [
                    'registration_id' => $registration->id,
                    'total_score' => $form_score->total_score,
                ];
            }
            Candidate::insert($candidates);
            unset($candidates);
        }
    }
}
