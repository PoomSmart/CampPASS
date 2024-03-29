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

use App\Notifications\NewCampRegistered;
use App\Notifications\CamperStatusChanged;
use App\Notifications\ApplicationStatusUpdated;

use App\BadgeController;
use App\Http\Controllers\CampController;
use App\Http\Controllers\CampApplicationController;
use App\Http\Controllers\CandidateController;

use App\Enums\QuestionType;
use App\Enums\ApplicationStatus;
use App\Enums\EducationLevel;

use Illuminate\Http\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    private static $debug = false;

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

    private function log_debug(string $message)
    {
        if (!self::$debug) return;
        logger()->debug($message);
    }

    private function programs()
    {
        $this->log_seed('programs');
        Program::insert([
            [ 'name' => 'Sci-Math' ],
            [ 'name' => 'Arts-Math' ],
            [ 'name' => 'Vocational/Diploma Cert.' ],
            [ 'name' => 'Other' ],
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
            [ 'name' => 'Primary School', 'short_name' => 'Primary' ],
            [ 'name' => 'Secondary School', 'short_name' => 'Secondary' ],
            [ 'name' => 'Junior High School', 'short_name' => 'Junior' ],
            [ 'name' => 'Senior High School', 'short_name' => 'Senior' ],
            [ 'name' => 'University', 'short_name' => 'University' ],
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

    private function student_documents()
    {
        $this->log_seed('student documents');
        $fake_document = new File(base_path('database/seeds/files/pdf.pdf'));
        foreach (User::campers()->where('status', 1)->get() as $camper) {
            $directory = Common::userFileDirectory($camper->id);
            Storage::putFileAs($directory, $fake_document, 'transcript.pdf');
            Storage::putFileAs($directory, $fake_document, 'confirmation_letter.pdf');
        }
        unset($fake_document);
    }

    private function randomID($camp_id)
    {
        return $camp_id.'-'.Common::randomString(10);
    }

    private function generate_answers(&$camp, &$json, $json_id, $question_type, $question_set_id, $question_id, $question_full_score, $graded, &$answers, &$has_any_answers, &$can_manual_grade, &$multiple_radio_map, &$multiple_checkbox_map, &$faker, $real_answers_json = null)
    {
        // For each question, all campers who are eligible and registered get a chance to answer
        foreach ($camp->registrations as $registration) {
            // If the registration is in applied or chosen state, answers must be there
            // If the registration is in draft state, answers may or may not be there
            if (!(($registration->applied() || $registration->chosen()) || (Common::randomRareHit() && $registration->status == ApplicationStatus::DRAFT)))
                continue;
            $answer = null;
            $score = null;
            switch ($question_type) {
                case QuestionType::TEXT:
                    $answer = $real_answers_json ? Common::randomElement($real_answers_json[$json_id]) : $faker->text($maxNbChars = 12);
                    break;
                case QuestionType::PARAGRAPH:
                    $answer = $real_answers_json ? Common::randomElement($real_answers_json[$json_id]) : $faker->sentences($nb = rand(1, 3), $asText = true);
                    break;
                case QuestionType::CHOICES:
                    // A bit of cheating in hope for seeded campers to get more scores
                    $answer = $graded && Common::randomRareHit() && isset($json['radio']) ? $json['radio'][$json_id] : array_rand($multiple_radio_map[$json_id]);
                    $score = $graded && isset($json['radio']) && $answer == $json['radio'][$json_id] ? $question_full_score : 0.0;
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
                // If the answer does not exist, this as much is of file type and we have yet to "seed" that
                'score' => is_null($answer) ? 0.0: !is_null($score) ? $score : 0.5 * rand(0, $question_full_score / 0.5),
            ];
        }
    }

    private function registrations_and_questions_and_answers()
    {
        $real_question_sets_seed_path = base_path('database/seeds/questions');
        $real_answers_seed_path = base_path('database/seeds/answers');
        $real_question_sets = array_diff(scandir($real_question_sets_seed_path), array_merge(array('..', '.'), array_map(function ($v) {
            return "{$v}.json";
        }, Common::$has_match_camp_ids)));
        $minimum_questions = 5;
        $maximum_questions = 8;
        $maximum_choices = 5;
        $maximum_checkboxes = 5;
        $faker = Faker\Factory::create();
        $campers = User::campers()->get();
        // First, fake registrations of campers who are eligible for
        $this->log_seed('registrations');
        $registrations = [];
        $form_scores = [];
        $camp_maker_notifications = [];
        $registration_id = 0;
        $dummy_file = new File(base_path('database/seeds/files/pdf.pdf'));
        $approved_camps = Camp::allApproved()->get();
        foreach (User::campers()->cursor() as $camper) {
            if (Common::randomRareHit()) // Say some campers have yet to do anything at all
                continue;
            $done = false;
            foreach ($approved_camps->filter(function ($camp) use (&$camper) {
                try {
                    $camper->isEligibleForCamp($camp);
                } catch (\Exception $e) {
                    return false;
                }
                return Common::randomMediumHit();
            }) as $camp) {
                $done = true;
                CampController::approve($camp, $silent = true);
                if (Common::randomRareHit()) // Say some campers have yet to apply for some camps
                    continue;
                // Randomly submit the application forms, taking into account its camp procedure
                $camp_procedure = $camp->camp_procedure;
                if ($camp_procedure->walkIn()) // Walk-in
                    $status = Common::randomFrequentHit() ? ApplicationStatus::CHOSEN : ApplicationStatus::DRAFT;
                else if ($camp->paymentOnly()) // Payment Only
                    $status = ApplicationStatus::CHOSEN;
                else // Anything else with QA
                    $status = Common::randomFrequentHit() ? ApplicationStatus::APPLIED : ApplicationStatus::DRAFT;
                ++$registration_id;
                $app_close_date = Common::parseDate($camp->app_close_date)->format('Y-m-d H:i:s');
                $registrations[] = [
                    'camp_id' => $camp->id,
                    'camper_id' => $camper->id,
                    'status' => $status,
                    'submission_time' => $faker->dateTimeBetween("{$app_close_date} -20 days", $app_close_date),
                ];
                if ($status >= ApplicationStatus::APPLIED) {
                    if (!isset($camp_maker_notifications[$camp->id]))
                        $camp_maker_notifications[$camp->id] = [];
                    $camp_maker_notifications[$camp->id][] = $registration_id;
                    if ($camp->application_fee && Common::randomFrequentHit()) {
                        $payment_directory = Common::paymentDirectory($camp->id);
                        Storage::putFileAs($payment_directory, $dummy_file, "payment_{$registration_id}.pdf");
                    }
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
        // Notify all the camp makers about all campers that applied for their camp
        $this->log('-> notifying camp makers about application forms coming in');
        foreach ($camp_maker_notifications as $camp_id => $registration_ids) {
            $campmakers = Camp::find($camp_id)->camp_makers();
            if ($campmakers->isNotEmpty()) {
                foreach ($registration_ids as $registration_id) {
                    $registration = Registration::find($registration_id);
                    foreach ($campmakers as $campmaker) {
                        $campmaker->notify(new CamperStatusChanged($registration));
                    }
                }
            }
        }
        unset($camp_maker_notifications);
        // Fake questions of several types for the camps that require
        $this->log_seed('questions and answers');
        $answers = [];
        $pairs = [];
        $questions = [];
        $question_sets = [];
        $question_set_id = $question_id = 0;
        $question_full_score = 10;
        foreach (Camp::allApproved()->cursor() as $camp) {
            if (!$camp->camp_procedure->candidate_required)
                continue;
            ++$question_set_id;
            $question_set_has_grade = $question_set_has_manual_grade = $has_any_answers = false;
            $question_set_total_score = 0;
            $question_set_try_auto = null;
            $multiple_radio_map = [];
            $multiple_checkbox_map = [];
            $matched = Common::hasMatch($camp);
            if ($matched || Common::randomMediumHit()) {
                // Use the real question sets
                $real_question_set = $matched ? "{$camp->id}.json" : Common::randomElement($real_question_sets);
                $json_path = "{$real_question_sets_seed_path}/{$real_question_set}";
                $json = json_decode(file_get_contents($json_path), true);
                // Append the camp ID to every question ID
                foreach (['type', 'question', 'question_required', 'question_graded', 'radio', 'radio_label', 'checkbox_label'] as $key) {
                    if (isset($json[$key])) {
                        if ($key == 'question_graded')
                            $question_set_has_grade = true;
                        $json[$key] = array_combine(array_map(function($k) use (&$json, &$camp, &$question_set_has_grade, &$question_set_has_manual_grade, &$question_set_total_score) {
                            $new_k = "{$camp->id}-{$k}";
                            if ($question_set_has_grade) {
                                if ($json['question'][$new_k] != QuestionType::CHOICES)
                                    $question_set_has_manual_grade = true;
                                $question_set_total_score += 10;
                            }
                            return $new_k;
                        }, array_keys($json[$key])), $json[$key]);
                        if ($key == 'checkbox_label' || $key == 'radio_label') {
                            foreach ($json[$key] as $id => $options) {
                                $json[$key][$id] = array_combine(array_map(function($k) use (&$camp) { return "{$camp->id}-{$k}"; }, array_keys($json[$key][$id])), $json[$key][$id]);
                                if ($key == 'radio_label')
                                    $multiple_radio_map[$id] = $json[$key][$id];
                                else
                                    $multiple_checkbox_map[$id] = $json[$key][$id];
                            }
                        } else if ($key == 'radio') {
                            foreach ($json[$key] as $id => $solution) {
                                $json[$key][$id] = "{$camp->id}-{$solution}";
                            }
                        }
                    }
                }
                $self_question_id = $question_id;
                $question_set = QuestionManager::createOrUpdateQuestionSet($camp, $json, $question_set_has_grade && $question_set_total_score ? $question_set_total_score * Common::floatRand(0.5, 0.8) : null, $extra_question_set_info = [
                    'id' => $question_set_id,
                    'manual_required' => $question_set_has_manual_grade,
                ], $question_id);
                $real_answers_json_path = "{$real_answers_seed_path}/{$real_question_set}";
                $real_answers_json = file_exists($real_answers_json_path) ? json_decode(file_get_contents($real_answers_json_path), true) : null;
                if ($real_answers_json) {
                    $real_answers_json['answer'] = array_combine(array_map(function($k) use (&$camp) { return "{$camp->id}-{$k}"; }, array_keys($real_answers_json['answer'])), $real_answers_json['answer']);
                    $real_answers_json = $real_answers_json['answer'];
                }
                foreach ($json['type'] as $json_id => $question_type) {
                    $graded = isset($json['question_graded'][$json_id]);
                    $this->generate_answers($camp, $json, $json_id, $question_type, $question_set_id, ++$self_question_id, $question_full_score, $graded, $answers, $has_any_answers, $can_manual_grade, $multiple_radio_map, $multiple_checkbox_map, $faker, $real_answers_json);
                }
                $question_set->update([
                    'finalized' => $has_any_answers,
                ]);
                unset($json);
            } else {
                $json = [];
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
                            for ($i = 1; $i <= $choices_number; ++$i) {
                                $choice_id = $this->randomID($camp->id);
                                $json['radio_label'][$json_id][$choice_id] = $faker->text($maxNbChars = 15);
                            }
                            $multiple_radio_map[$json_id] = $json['radio_label'][$json_id];
                            $correct_choice = array_rand($json['radio_label'][$json_id]);
                            $json['radio'][$json_id] = $correct_choice;
                            break;
                        case QuestionType::CHECKBOXES:
                            if ($graded) $question_set_has_manual_grade = true;
                            $checkboxes_number = rand($maximum_checkboxes / 2, $maximum_checkboxes);
                            $json['checkbox_label'][$json_id] = [];
                            for ($i = 1; $i <= $checkboxes_number; ++$i) {
                                $checkbox_id = $this->randomID($camp->id);
                                $json['checkbox_label'][$json_id][$checkbox_id] = $faker->text($maxNbChars = 15);
                            }
                            $multiple_checkbox_map[$json_id] = $json['checkbox_label'][$json_id];
                            break;
                    }
                    ++$question_id;
                    $questions[] = [
                        'id' => $question_id,
                        'json_id' => $json_id,
                        'type' => $question_type,
                        'full_score' => $graded ? $question_full_score : null,
                    ];
                    if ($graded)
                        $question_set_total_score += $question_full_score;
                    $pairs[] = [
                        'question_set_id' => $question_set_id,
                        'question_id' => $question_id,
                    ];
                    $this->generate_answers($camp, $json, $json_id, $question_type, $question_set_id, $question_id, $question_full_score, $graded, $answers, $has_any_answers, $can_manual_grade, $multiple_radio_map, $multiple_checkbox_map, $faker);
                }
                $question_sets[] = [
                    'id' => $question_set_id,
                    'camp_id' => $camp->id,
                    'minimum_score' => $question_set_has_grade && $question_set_total_score ? $question_set_total_score * Common::floatRand(0.5, 0.8) : null,
                    'manual_required' => $question_set_has_manual_grade,
                    'total_score' => $question_set_total_score,
                    'finalized' => $has_any_answers,
                ];
                // Empty fields are ruled out the same way the POST form does
                foreach ($json as $key => $value) {
                    if (empty($value))
                        unset($json[$key]);
                }
                QuestionManager::writeQuestionJSON($camp->id, $json);
                unset($json);
            }
            unset($multiple_radio_map);
            unset($multiple_checkbox_map);
            // We wouldn't normally create a form score record for any draft application forms
            foreach ($camp->registrations->where('status', '>=', ApplicationStatus::APPLIED) as $registration) {
                $form_scores[] = [
                    'registration_id' => $registration->id,
                    'question_set_id' => $question_set_id,
                    'camp_id' => $camp->id,
                    // We cannot calculate the total score right now
                    'total_score' => null,
                    // Form scores are finalized as we say every question can be auto-graded
                    // However, this must mean there are no manual graded questions
                    'finalized' => (!is_null($question_set_try_auto) ? $question_set_try_auto : true) && !$question_set_has_manual_grade,
                ];
            }
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
        unset($faker);
        // At this point, we simulate candidates announcement and attendance confirmation
        $this->log('-> simulating candidates announcement and attendance confirmation');
        foreach (Camp::allApproved()->get() as $camp) {
            if (Common::randomMediumHit()) {
                $camp_directory = Common::publicCampDirectory($camp->id);
                Storage::putFileAs($camp_directory, $dummy_file, 'parental_consent.pdf');
                $camp->update([
                    'parental_consent' => 'parental_consent.pdf',
                ]);
            }
            $question_set = $camp->question_set;
            $has_question_set = !is_null($question_set);
            if ($has_question_set) {
                if (!$question_set->total_score) {
                    $camp->update([
                        'backup_limit' => null,
                    ]);
                }
                // Question sets must be finalized first before the ranking could happen
                $question_set->update([
                    'finalized' => true,
                ]);
            }
            $has_consent = $camp->parental_consent;
            $consent_directory = $has_consent ? Common::consentDirectory($camp->id) : null;
            $payment_directory = $camp->hasPayment() ? Common::paymentDirectory($camp->id) : null;
            $matched = Common::hasMatch($camp);
            try {
                $registrations = $camp->registrations;
                $form_scores = CandidateController::create_form_scores($camp, $question_set, $registrations);
                if ($has_question_set) {
                    if ((!$matched && Common::randomRareHit()) || $registrations->isEmpty())
                        continue;
                    $campmakers = $camp->camp_makers();
                    foreach ($form_scores->get() as $form_score) {
                        $registration = $form_score->registration;
                        try {
                            CandidateController::form_finalize($form_score, $silent = true);
                        } catch (\Exception $e) {}
                        // We can seed payment slips for the camps that require application fee here
                        // This is because the campers have to do it at the beginning
                        if ($camp->application_fee && Common::randomVeryFrequentHit())
                            Storage::putFileAs($payment_directory, $dummy_file, "payment_{$registration->id}.pdf");
                        if ($has_consent && Common::randomVeryFrequentHit())
                            Storage::putFileAs($consent_directory, $dummy_file, "consent_{$registration->id}.pdf");
                        if ($campmakers->count()) {
                            try {
                                CandidateController::document_approve($registration, $approved_by_id = $campmakers->random()->id);
                            } catch (\Exception $e) {}
                        }
                    }
                    $no_form_scores_error = true;
                    try {
                        $form_scores = CandidateController::rank($camp, $list = true, $without_withdrawn = true, $without_returned = true, $check_consent_paid = true);
                    } catch (\Exception $e) {
                        $this->log_debug("Announcement/Confirmation Simulation Ranked: {$e}");
                        $no_form_scores_error = false;
                    }
                    $interview_announce = null;
                    if ($no_form_scores_error) {
                        $camp_procedure = $camp->camp_procedure;
                        $interview_required = $camp_procedure->interview_required;
                        try {
                            CandidateController::announce($camp, $silent = true, $form_scores = $form_scores);
                        } catch (\Exception $e) {
                            $this->log_debug("Announcement/Confirmation Simulation Announced: {$e}");
                        }
                        foreach ($camp->candidates->where('backup', false) as $candidate) {
                            if (Common::randomRareHit())
                                continue;
                            if (Common::randomVeryFrequentHit()) {
                                $registration = $candidate->registration;
                                $proceed = $interview_required ? Common::randomFrequentHit() : true;
                                if ($interview_required)
                                    CandidateController::interview_check_real($registration, $proceed ? 'true' : 'false');
                                if ($proceed) {
                                    if ($interview_required && is_null($interview_announce))
                                        $interview_announce = Common::randomFrequentHit();
                                    if ($interview_announce)
                                        $registration->camper->notify(new ApplicationStatusUpdated($registration));
                                    // We can seed payment slips for the camps that require deposit here
                                    // This is because the campers can only do this after they know they are chosen
                                    try {
                                        if ($camp_procedure->deposit_required && Common::randomVeryFrequentHit())
                                            Storage::putFileAs($payment_directory, $dummy_file, "payment_{$registration->id}.pdf");
                                        if (Common::randomFrequentHit() && $campmakers->count()) {
                                            CandidateController::document_approve($registration, $approved_by_id = $campmakers->random()->id);
                                            if (Common::randomMediumHit())
                                                CampApplicationController::confirm($registration, $silent = true);
                                        }
                                    } catch (\Exception $e) {
                                        $this->log_debug("Announcement/Confirmation Simulation Nested: {$e}");
                                    }
                                }
                            } else if (Common::randomRareHit()) {
                                try {
                                    CampApplicationController::withdraw($registration, $silent = true);
                                } catch (\Exception $e) {}
                            }
                        }
                    }
                    // TODO: This is not really working?
                    if ($interview_announce) {
                        $camp->update([
                            'interview_announced' => true,
                        ]);
                    }
                } else {
                    $has_payment = $camp->paymentOnly();
                    $payment_directory = $has_payment ? Common::paymentDirectory($camp->id) : null;
                    foreach ($camp->registrations->where('status', '>=', ApplicationStatus::APPLIED) as $registration) {
                        if ($has_payment && Common::randomVeryFrequentHit())
                            Storage::putFileAs($payment_directory, $dummy_file, "payment_{$registration->id}.pdf");
                        if ($has_consent && Common::randomVeryFrequentHit())
                            Storage::putFileAs($consent_directory, $dummy_file, "consent_{$registration->id}.pdf");
                        if (Common::randomFrequentHit() && $campmakers->count()) {
                            try {
                                CandidateController::document_approve($registration, $approved_by_id = $campmakers->random()->id);
                                if (Common::randomMediumHit())
                                    CampApplicationController::confirm($registration, $silent = true);
                            } catch (\Exception $e) {
                                $this->log_debug("Announcement/Confirmation Simulation Nested 2: {$e}");
                            }
                        } else if (Common::randomRareHit()) {
                            try {
                                CampApplicationController::withdraw($registration, $silent = true);
                            } catch (\Exception $e) {}
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->log_debug("Announcement/Confirmation Simulation: {$e}");
            }
        }        
        unset($dummy_file);
        // Simulate badges generation
        $this->log('-> simulating badges generation');
        foreach (Registration::all() as $registration) {
            BadgeController::addBadgeIfNeeded($registration);
        }
    }

    private function camps()
    {
        $this->call(CampTableSeeder::class);
        // Assign the real banner and poster to each camp that has them
        $this->log('-> assigning banner and poster to each camp');
        $camp_resource_directory = base_path('database/seeds/camps');
        foreach (Camp::all() as $camp) {
            // Fix up acceptable_education_levels
            $levels = [];
            foreach ($camp->acceptable_education_levels as $level) {
                $mapped = User::$year_to_education_level[$level];
                if (is_array($mapped)) {
                    foreach ($mapped as $l) {
                        if (!in_array($l, $levels))
                            $levels[] = $l;
                    }
                } else if (!in_array($mapped, $levels))
                    $levels[] = $mapped;
            }
            $camp->update([
                'acceptable_education_levels' => $levels,
            ]);
            unset($levels);
            $directory = Common::publicCampDirectory($camp->id);
            foreach (['banner', 'poster'] as $filename) {
                try {
                    $camp_id = $camp->id;
                    if ($camp_id >= 45 && $camp_id <= 48)
                        $camp_id = '45-48';
                    $resource = file_get_contents("{$camp_resource_directory}/{$filename}/{$camp_id}.jpg");
                    $camp->update([
                        $filename => "{$filename}.jpg",
                    ]);
                    Storage::put("$directory/{$filename}.jpg", $resource);
                } catch (\Exception $e) {}
            }
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
            'name_en' => 'Pakpoom',
            'surname_en' => 'Rukrian',
            'name_th' => 'ภาคภูมิ',
            'surname_th' => 'รักเรียน',
            'gender' => 0,
            'education_level' => EducationLevel::M5,
            'cgpa' => 3.6,
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
            'gender' => 1,
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
        // Notify the admin about unapproved camps
        foreach (Camp::allNotApproved()->cursor() as $camp) {
            $admin->notify(new NewCampRegistered($camp));
        }
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
        $this->log_seed('users');
        for ($i = 1; $i <= 3; ++$i) {
            factory(User::class, 150)->create();
            $c = $i * 150;
            $this->log_seed("users: {$c} seeded");
        }
        $this->camps();
        $this->student_documents();
        $this->registrations_and_questions_and_answers();
        $this->alter_campers();
        $this->alter_campmakers();
        $this->create_admin();
        $this->call(PermissionTableSeeder::class);
        Model::reguard();
    }
}
