<?php

use App\Answer;
use App\Badge;
use App\BadgeCategory;
use App\Camp;
use App\CampCategory;
use App\CampProcedure;
use App\Common;
use App\FormScore;
use App\User;
use App\Program;
use App\Registration;
use App\Region;
use App\Religion;
use App\School;
use App\Organization;
use App\Question;
use App\QuestionSet;
use App\QuestionSetQuestionPair;
use App\QuestionManager;
use App\Year;

use App\Imports\ProvincesImport;

use App\BadgeController;
use App\Http\Controllers\CampApplicationController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\QualificationController;

use App\Enums\QuestionType;
use App\Enums\ApplicationStatus;

use Spatie\Permission\Models\Role;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    private function log(string $message)
    {
        $app = config('app.name');
        $this->command->line("<comment>{$app}: </comment>{$message}");
    }

    private function log_seed(string $message)
    {
        $this->log("seeding {$message}");
    }

    private function log_alter(string $message)
    {
        $this->log("alter {$message}");
    }

    private function programs()
    {
        $this->log_seed('programs');
        Program::insert([
            [ 'name' => 'Sci-Math' ],
            [ 'name' => 'Arts-Math' ],
            [ 'name' => 'Vocational/Diploma Cert.' ],
        ]);
    }

    private function regions()
    {
        $this->log_seed('regions');
        Region::insert([
            [ 'name' => 'Northen Thailand', 'short_name' => 'N' ],
            [ 'name' => 'Northeastern Thailand', 'short_name' => 'NE' ],
            [ 'name' => 'Western Thailand', 'short_name' => 'W' ],
            [ 'name' => 'Central Thailand', 'short_name' => 'C' ],
            [ 'name' => 'Eastern Thailand', 'short_name' => 'E' ],
            [ 'name' => 'Southern Thailand', 'short_name' => 'S' ],
        ]);
    }

    private function camp_categories()
    {
        $this->log_seed('camp_categories');
        CampCategory::insert([
            [ 'name' => 'Engineering' ],
            [ 'name' => 'Science' ],
            [ 'name' => 'Computer' ],
            [ 'name' => 'Education' ],
            [ 'name' => 'Architectural' ],
            [ 'name' => 'Law' ],
            [ 'name' => 'Language-Human' ],
            [ 'name' => 'Commart' ],
            [ 'name' => 'Health' ],
            [ 'name' => 'Doctor' ],
            [ 'name' => 'Nurse' ],
            [ 'name' => 'Dentist' ],
            [ 'name' => 'Psychology' ],
            [ 'name' => 'Pharmacy' ],
            [ 'name' => 'Music' ],
            [ 'name' => 'Tutor' ],
            [ 'name' => 'Account-Economic' ],
            [ 'name' => 'Social-Science' ],
            [ 'name' => 'Veter' ],
            [ 'name' => 'Art' ],
            [ 'name' => 'Agri-Fishery' ],
            [ 'name' => 'Political' ],
            [ 'name' => 'Youth' ],
            [ 'name' => 'Preserve' ],
        ]);
    }

    private function camp_procedures()
    {
        $this->log_seed('camp_procedures');
        CampProcedure::insert([
            [ 'title' => 'Walk-in', 'description' => 'WalkInDescription', 'interview_required' => false, 'deposit_required' => false, 'candidate_required' => false ],
            [ 'title' => 'QA', 'description' =>'QAOnlyDescription', 'interview_required' => false, 'deposit_required' => false, 'candidate_required' => true ],
            [ 'title' => 'Deposit', 'description' => 'DepositOnlyDescription', 'interview_required' => false, 'deposit_required' => true, 'candidate_required' => false ],
            [ 'title' => 'QA-Deposit', 'description' => 'QAAndDepositDescription', 'interview_required' => false, 'deposit_required' => true, 'candidate_required' => true ],
            [ 'title' => 'QA-Interview', 'description' => 'QAAndInterviewDescription', 'interview_required' => true, 'deposit_required' => false, 'candidate_required' => true ],
            [ 'title' => 'QA-Interview-Deposit', 'description' => 'QAAndInterviewAndDepositDescription', 'interview_required' => true, 'deposit_required' => true, 'candidate_required' => true ],
        ]);
    }

    private function religions()
    {
        $this->log_seed('religions');
        Religion::insert([
            [ 'name' => 'Buddhist' ],
            [ 'name' => 'Christ' ],
            [ 'name' => 'Islamic' ],
            [ 'name' => 'Other' ],
        ]);
    }

    private function years()
    {
        $this->log_seed('years');
        Year::insert([
            [ 'name' => 'Primary School' ],
            [ 'name' => 'Secondary School' ],
            [ 'name' => 'Junior High School' ],
            [ 'name' => 'Senior High School' ],
        ]);
    }

    private function provinces()
    {
        Excel::import(new ProvincesImport, 'provinces.csv', 'seed', \Maatwebsite\Excel\Excel::CSV);
    }

    private function badge_categories()
    {
        $this->log_seed('badge_categories');
        BadgeCategory::insert([
            [ 'name' => '3 Stars Engineering', 'description' => 'badge.3StarsEngineerDescription' ],
            [ 'name' => '3 Stars Science', 'description' => 'badge.3StarsScienceDescription' ],
            [ 'name' => '3 Stars Computer', 'description' => 'badge.3StarsComputerDescription' ],
            [ 'name' => '3 Stars Education', 'description' => 'badge.3StarsEducationDescription' ],
            [ 'name' => '3 Stars Architectural' , 'description' => 'badge.3StarsArchitecturalDescription' ],
            [ 'name' => '3 Stars Law', 'description' => 'badge.3StarsLawDescription' ],
            [ 'name' => '3 Stars Language-Human', 'description' => 'badge.3StarsLanguage-HumanDescription' ],
            [ 'name' => '3 Stars Commart', 'description' => 'badge.3StarsCommartDescription' ],
            [ 'name' => '3 Stars Health', 'description' => 'badge.3StarsHealthDescription' ],
            [ 'name' => '3 Stars Doctor', 'description' => 'badge.3StarsDoctorDescription' ],
            [ 'name' => '3 Stars Nurse', 'description' => 'badge.3StarsNurseDescription' ],
            [ 'name' => '3 Stars Dentist', 'description' => 'badge.3StarsDentistDescription' ],
            [ 'name' => '3 Stars Psychology', 'description' => 'badge.3StarsPsychologyDescription' ],
            [ 'name' => '3 Stars Pharmacy', 'description' => 'badge.3StarsPharmacyDescription' ],
            [ 'name' => '3 Stars Music', 'description' => 'badge.3StarsMusicDescription' ],
            [ 'name' => '3 Stars Tutor', 'description' => 'badge.3StarsTutorDescription' ],
            [ 'name' => '3 Stars Account-Economic', 'description' => 'badge.3StarsAccount-EconomicDescription' ],
            [ 'name' => '3 Stars Social Science', 'description' => 'badge.3StarsSocialScienceDescription' ],
            [ 'name' => '3 Stars Veter' , 'description' => 'badge.3StarsVeterDescription' ],
            [ 'name' => '3 Stars Art' , 'description' => 'badge.3StarsArtDescription' ],
            [ 'name' => '3 Stars Agri-Fishery' , 'description' => 'badge.3StarsAgri-FisheryDescription' ],
            [ 'name' => '3 Stars Political' , 'description' => 'badge.3StarsPoliticalDescription' ],
            [ 'name' => '3 Stars Youth' , 'description' => 'badge.3StarsYouthrDescription' ],
            [ 'name' => '3 Stars Preserve' , 'description' => 'badge.3StarsPreserveDescription' ],
            [ 'name' => 'Pioneer', 'description' => 'badge.PioneerDescription' ],
            [ 'name' => 'Premium', 'description' => 'badge.PremiumDescription' ],
            [ 'name' => 'BabyStep', 'description' => 'badge.BabyStepDescription' ],
        ]);
    }

    private function randomID($camp_id)
    {
        return $camp_id.'-'.Common::randomString(10);
    }

    private function registrations_and_questions_and_answers()
    {
        $minimum_questions = 6;
        $maximum_questions = 15;
        $maximum_choices = 6;
        $maximum_checkboxes = 6;
        $faker = Faker\Factory::create();
        $campers = User::campers()->get();
        // First, fake registrations of campers who are eligible for
        $this->log_seed('registrations');
        $registrations = [];
        $form_scores = [];
        foreach (User::campers()->cursor() as $camper) {
            if (Common::randomRareHit()) // Say some campers have yet to do anything at all
                continue;
            $done = false;
            foreach (Camp::all()->filter(function ($camp) use (&$camper) {
                try {
                    $camper->isEligibleForCamp($camp);
                } catch (\Exception $e) {
                    return false;
                }
                return Common::randomMediumHit() && !$camp->getRegistrations($camper)->limit(1)->exists();
            }) as $camp) {
                $done = true;
                if (Common::randomFrequentHit()) // Say some campers have yet to apply for some camps
                    continue;
                // Randomly submit the application forms, taking into account its camp procedure
                $camp_procedure = $camp->camp_procedure;
                if (!$camp_procedure->deposit_required && !$camp_procedure->interview_required && !$camp_procedure->candidate_required) // Walk-in
                    $status = Common::randomFrequentHit() ? ApplicationStatus::QUALIFIED : ApplicationStatus::DRAFT;
                else if ($camp_procedure->deposit_required) // Deposit Only
                    $status = ApplicationStatus::CHOSEN; // TODO: We have yet to seed payment slips
                else // Anything else with QA
                    $status = Common::randomFrequentHit() ? ApplicationStatus::APPLIED : ApplicationStatus::DRAFT;
                $registrations[] = [
                    'camp_id' => $camp->id,
                    'camper_id' => $camper->id,
                    'status' => $status,
                    'submission_time' => now(),
                ];
                // Camps with registrations must obviously be approved first
                if (!$camp->approved) {
                    $camp->update([
                        'approved' => true,
                    ]);
                }
            }
            if ($done) {
                // Campers that applied for camps must already have their account activated
                $camper->activate();
            }
        }
        foreach (array_chunk($registrations, 1000) as $chunk)
            Registration::insert($chunk);
        unset($registrations);
        // Fake questions of several types for the camps that require
        $this->log_seed('questions and answers');
        $answers = [];
        $pairs = [];
        $questions = [];
        $question_sets = [];
        $question_set_id = 0;
        $question_id = 0;
        foreach (Camp::allApproved()->cursor() as $camp) {
            if (!$camp->camp_procedure->candidate_required)
                continue;
            $json = [];
            ++$question_set_id;
            $question_set_has_grade = false;
            $question_set_has_manual_grade = false;
            $question_set_total_score = 0;
            // Biased setting to have some entirely auto-gradable question sets
            $question_set_try_auto = Common::randomRareHit();
            $json['camp_id'] = $camp->id;
            $json['type'] = [];
            $json['question'] = [];
            $json['question_required'] = [];
            $json['question_graded'] = [];
            $json['radio'] = [];
            $json['radio_label'] = [];
            $json['checkbox_label'] = [];
            $questions_number = rand($minimum_questions, $maximum_questions);
            $has_any_answers = false;
            while ($questions_number--) {
                $question_type = $question_set_try_auto ? QuestionType::CHOICES : QuestionType::any();
                // Requirement: file upload is always required and graded
                if ($question_type == QuestionType::FILE) {
                    $graded = true;
                    // Having gradable file upload automatically translates into needing to manually grade
                    $question_set_has_manual_grade = true;
                } else
                    $graded = $question_set_try_auto ? true : rand(0, 1);
                if ($graded)
                    $question_set_has_grade = true;
                $required = $graded ? true : rand(0, 1);
                $json_id = $this->randomID($camp->id);
                $json['type'][$json_id] = $question_type;
                $question_text = $faker->sentences($nb = rand(1, 2), $asText = true);
                $json['question'][$json_id] = $question_text;
                if ($required)
                    $json['question_required'][$json_id] = '1';
                if ($graded)
                    $json['question_graded'][$json_id] = '1';
                $multiple_radio_map = [];
                $multiple_checkbox_map = [];
                switch ($question_type) {
                    case QuestionType::TEXT:
                        if ($graded) $question_set_has_manual_grade = true;
                        break;
                    case QuestionType::PARAGRAPH:
                        if ($graded) $question_set_has_manual_grade = true;
                        break;
                    case QuestionType::CHOICES:
                        $choices_number = rand($maximum_choices / 2, $maximum_choices);
                        $json['radio_label'][$json_id] = [];
                        for ($i = 1; $i <= $choices_number; $i++) {
                            $choice_id = $this->randomID($camp->id);
                            $json['radio_label'][$json_id][$choice_id] = $faker->text($maxNbChars = 40);
                        }
                        $multiple_radio_map[$json_id] = $json['radio_label'][$json_id];
                        $correct_choice = array_rand($json['radio_label'][$json_id]);
                        $json['radio'][$json_id] = $correct_choice;
                        break;
                    case QuestionType::CHECKBOXES:
                        if ($graded) $question_set_has_manual_grade = true;
                        $checkboxes_number = rand($maximum_checkboxes / 2, $maximum_checkboxes);
                        $json['checkbox_label'][$json_id] = [];
                        for ($i = 1; $i <= $checkboxes_number; $i++) {
                            $checkbox_id = $this->randomID($camp->id);
                            $json['checkbox_label'][$json_id][$checkbox_id] = $faker->text($maxNbChars = 40);
                        }
                        $multiple_checkbox_map[$json_id] = $json['checkbox_label'][$json_id];
                        break;
                }
                ++$question_id;
                $question_full_score = 10;
                $questions[] = [
                    'json_id' => $json_id,
                    'type' => $question_type,
                    'full_score' => $graded ? $question_full_score : null,
                ];
                $question_set_total_score += $question_full_score;
                $pairs[] = [
                    'question_set_id' => $question_set_id,
                    'question_id' => $question_id,
                ];
                if (Common::randomFrequentHit()) {
                    // For each question, all campers who are eligible and registered get a chance to answer
                    foreach ($camp->registrations as $registration) {
                        // If the registration is in applied state, answers must be there
                        // If the registration is in draft state, answers may or may not be there
                        if (!($registration->applied() || (Common::randomRareHit() && $registration->status == ApplicationStatus::DRAFT)))
                            continue;
                        $answer = null;
                        switch ($question_type) {
                            case QuestionType::TEXT:
                                $answer = $faker->text($maxNbChars = 20);
                                break;
                            case QuestionType::PARAGRAPH:
                                $answer = $faker->sentences($nb = rand(2, 5), $asText = true);
                                break;
                            case QuestionType::CHOICES:
                                // A bit of cheating in hope for seeded campers to get more scores
                                $answer = Common::randomRareHit() ? $json['radio'][$json_id] : array_rand($multiple_radio_map[$json_id]);
                                break;
                            case QuestionType::CHECKBOXES:
                                $count = rand(1, count($multiple_checkbox_map[$json_id]));
                                $answer = array_rand($multiple_checkbox_map[$json_id], $count);
                                if ($count == 1)
                                    $answer = [ $answer ];
                                break;
                            case QuestionType::FILE:

                                break;
                        }
                        $answer = QuestionManager::encodeIfNeeded($answer, $question_type);
                        $can_manual_grade = $graded;
                        $has_any_answers = true;
                        $answers[] = [
                            'question_set_id' => $question_set_id,
                            'question_id' => $question_id,
                            'camper_id' => $registration->camper_id,
                            'registration_id' => $registration->id,
                            'answer' => $answer,
                            // We take only the questions that need to be graded, i.e., full_score is set
                            // If the answer does not exist, this as much is of file type and we have yet to "seed" that
                            'score' => $answer ? $faker->randomFloat($nbMaxDecimals = 2, $min = 0.0, $max = $question_full_score) : 0.0,
                        ];
                    }
                }
                unset($multiple_radio_map);
                unset($multiple_checkbox_map);
            }
            // We wouldn't normally create a form score record for any draft application forms
            foreach ($camp->registrations->where('status', ApplicationStatus::APPLIED) as $registration) {
                $form_scores[] = [
                    'registration_id' => $registration->id,
                    'question_set_id' => $question_set_id,
                    // We cannot calculate the total score right now
                    'total_score' => null,
                    // Form scores are finalized as we say every question can be auto-graded
                    // However, this must mean there are no manual graded questions
                    'finalized' => $question_set_try_auto && !$question_set_has_manual_grade,
                ];
            }
            $question_sets[] = [
                'camp_id' => $camp->id,
                'score_threshold' => $question_set_has_grade ? rand(1, 75) / 100.0 : null,
                'manual_required' => $question_set_has_manual_grade,
                'total_score' => $question_set_total_score,
                'finalized' => $has_any_answers,
            ];
            if ($question_set_has_manual_grade && Common::randomVeryFrequentHit())
                $manual_grade_question_set_ids[] = $question_set_id;
            // Empty fields are ruled out the same way the form POST does
            foreach ($json as $key => $value) {
                if (empty($value))
                    unset($json[$key]);
            }
            QuestionManager::writeQuestionJSON($camp->id, $json);
            unset($json);
        }
        foreach (array_chunk($questions, 1000) as $chunk)
            Question::insert($chunk);
        unset($questions);
        foreach (array_chunk($question_sets, 1000) as $chunk)
            QuestionSet::insert($chunk);
        unset($question_sets);
        foreach (array_chunk($pairs, 1000) as $chunk)
            QuestionSetQuestionPair::insert($chunk);
        unset($pairs);
        foreach (array_chunk($form_scores, 1000) as $chunk)
            FormScore::insert($chunk);
        unset($form_scores);
        foreach (array_chunk($answers, 1000) as $chunk)
            Answer::insert($chunk);
        unset($answers);
        // Now we can mark the application forms with manual grading as finalized
        $this->log('-> finalizing some manually-graded form scores');
        foreach (FormScore::whereIn('question_set_id', $manual_grade_question_set_ids)->cursor() as $manual_form_score) {
            try {
                QualificationController::form_finalize($manual_form_score, $silent = true);
            } catch (\Exception $e) {
                continue;
            }
        }
        unset($manual_grade_question_set_ids);
        unset($faker);
        // At this point, we simulate candidates announcement and attendance confirmation
        $this->log('-> simulating candidates announcement and attendance confirmation');
        foreach (QuestionSet::all() as $question_set) {
            if (Common::randomRareHit())
                continue;
            try {
                CandidateController::announce($question_set, $void = true);
                if (Common::randomFrequentHit()) {
                    foreach ($question_set->camp->registrations->all() as $registration) {
                        if (Common::randomRareHit())
                            continue;
                        CampApplicationController::confirm($registration, $void = true);
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        // Simulate badges generation
        $this->log('-> simulating badges generation');
        foreach (Registration::all() as $registration) {
            BadgeController::addBadgeIfNeeded($registration);
        }
    }

    private function alter_campers()
    {
        $this->log_alter('campers');
        $candidate = User::campers(true)->get()->sortByDesc(function ($camper) {
            return $camper->badges->count();
        })->first();
        $candidate->update([
            'username' => 'camper',
            'cgpa' => 3.6, // The candidate will be used to test certain camps so the smartening is needed
            'type' => config('const.account.camper'),
        ]);
        $candidate->activate();
    }

    private function alter_campmakers()
    {
        $this->log_alter('campmakers');
        $candidate = User::campMakers(true)->get()->sortByDesc(function ($campmaker) {
            return $campmaker->getBelongingCamps()->count();
        })->first();
        $candidate->update([
            'username' => 'campmaker',
            'type' => config('const.account.campmaker'),
        ]);
        $candidate->activate();
    }

    private function create_admin()
    {
        $this->log_alter('admin');
        $admin = User::campMakers(true)->limit(1)->first();
        $admin->update([
            'type' => config('const.account.admin'),
            'username' => 'admin',
            'name_en' => 'Administrator',
            'surname_en' => '001',
            'nickname_en' => 'Admin',
            'organization_id' => null,
        ]);
        $admin->activate();
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::disableQueryLog();
        Model::unguard();
        $this->religions();
        $this->regions();
        $this->provinces();
        $this->years();
        $this->programs();
        $this->badge_categories();
        $this->camp_categories();
        $this->camp_procedures();
        $this->call(SchoolTableSeeder::class);
        $this->call(OrganizationTableSeeder::class);
        $this->log_seed('camps');
        factory(Camp::class, 400)->create();
        $this->log_seed('users');
        factory(User::class, 400)->create();
        $this->registrations_and_questions_and_answers();
        $this->alter_campers();
        $this->alter_campmakers();
        $this->create_admin();
        $this->call(PermissionTableSeeder::class);
        Model::reguard();
    }
}
