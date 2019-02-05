<?php

use App\Answer;
use App\Camp;
use App\CampCategory;
use App\CampProcedure;
use App\Common;
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
use App\Year;
use App\BadgeCategory;

use App\Enums\EducationLevel;
use App\Enums\QuestionType;
use App\Enums\RegistrationStatus;

use Spatie\Permission\Models\Role;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    private static function log(string $message)
    {
        Log::channel('errorlog')->info($message);
    }

    private static function log_seed(string $message)
    {
        self::log("seeding {$message}");
    }

    private static function log_alter(string $message)
    {
        self::log("alter {$message}");
    }

    private function programs()
    {
        $this->log_seed('programs');
        Program::insert([
            [ 'name' => 'Sci-Math' ],
            [ 'name' => 'Arts-Math' ],
            [ 'name' => 'Vocational/Diploma Cert.' ], // TODO: Localization
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
            [ 'name' => 'Southern Thailand', 'short_name' => 'S' ], // TODO: Localization
        ]);
    }

    private function camp_categories()
    {
        $this->log_seed('camp_categories');
        CampCategory::insert([
            [ 'name' => 'Engineering' ],
            [ 'name' => 'Science' ],
            [ 'name' => 'Computer' ],
            [ 'name' => 'Teacher' ],
            [ 'name' => 'Architectural' ],
            [ 'name' => 'Law' ],
            [ 'name' => 'Language-Human' ],
            [ 'name' => 'Commarts' ],
            [ 'name' => 'Health' ],
            [ 'name' => 'Doctor' ],
            [ 'name' => 'Nurse' ],
            [ 'name' => 'Dentist' ],
            [ 'name' => 'Psychology' ],
            [ 'name' => 'Pharmacy' ],
            [ 'name' => 'Music' ],
            [ 'name' => 'Tutor' ],
            [ 'name' => 'Account-Economic' ],
            [ 'name' => 'Social Science' ],
            [ 'name' => 'Veter' ],
            [ 'name' => 'Art' ],
            [ 'name' => 'Agri-Fishery' ],
            [ 'name' => 'Political' ],
            [ 'name' => 'Youth' ],
            [ 'name' => 'Preserve' ], // TODO: Localization
        ]);
    }

    private function camp_procedures()
    {
        $this->log_seed('camp_procedures');
        CampProcedure::insert([
            [ 'title' => 'Walk-in', 'description' => 'camp.WalkInDescription', 'interview_required' => false, 'deposit_required' => false, 'candidate_required' => false ],
            [ 'title' => 'QA Only', 'description' =>'camp.QAOnlyDescription', 'interview_required' => false, 'deposit_required' => false, 'candidate_required' => true ],
            [ 'title' => 'Deposit Only', 'description' => 'camp.DepositOnlyDescription', 'interview_required' => false, 'deposit_required' => true, 'candidate_required' => false ],
            [ 'title' => 'Interview Only', 'description' => 'camp.InterviewOnlyDescription', 'interview_required' => true, 'deposit_required' => false, 'candidate_required' => false ],
            [ 'title' => 'QA and Deposit', 'description' => 'camp.QAAndDepositDescription', 'interview_required' => false, 'deposit_required' => true, 'candidate_required' => true ],
            [ 'title' => 'QA and Interview', 'description' => 'camp.QAAndInterviewDescription', 'interview_required' => true, 'deposit_required' => false, 'candidate_required' => true ],
            [ 'title' => 'Interview and Deposit', 'description' => 'camp.InterviewAndDepositDescription', 'interview_required' => true, 'deposit_required' => true, 'candidate_required' => false ],
            [ 'title' => 'QA, Interview and Deposit', 'description' => 'camp.QAAndInterviewAndDepositDescription', 'interview_required' => true, 'deposit_required' => true, 'candidate_required' => true ],
        ]);
    }

    private function religions()
    {
        $this->log_seed('religions');
        Religion::insert([
            [ 'name' => 'Buddhist' ],
            [ 'name' => 'Christ' ],
            [ 'name' => 'Islamic' ],
            [ 'name' => 'Other' ], // TODO: Localization
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

    private function badge_categories()
    {
        $this->log_seed('badge_categories');
        BadgeCategory::insert([
            [ 'name' => 'Pioneer', 'description' => 'badge.PioneerDescription' ],
            [ 'name' => 'Premium', 'description' => 'badge.PremiumDescription' ],
            [ 'name' => 'Baby-Step', 'description' => 'badge.Baby-StepDescription' ],
            [ 'name' => '3 Stars Engineering', 'description' => 'badge.3StarsEngineerDescription' ],
            [ 'name' => '3 Stars Science', 'description' => 'badge.3 StarsScienceDescription' ],
            [ 'name' => '3 Stars Computer', 'description' => 'badge.3StarsComputerDescription' ],
            [ 'name' => '3 Stars Teacher', 'description' => 'badge.3StarsTeacherDescription' ],
            [ 'name' => '3 Stars Architectural' , 'description' => 'badge.3StarsArchitecturalDescription' ],
            [ 'name' => '3 Stars Law', 'description' => 'badge.3StarsLawDescription' ],
            [ 'name' => '3 Stars Language-Human', 'description' => 'badge.3StarsLanguage-HumanDescription' ],
            [ 'name' => '3 Stars Commarts', 'description' => 'badge.3StarsCommartsDescription' ],
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
        ]);
    }

    private function randomID($camp_id)
    {
        return $camp_id.'-'.Common::randomString(10);
    }

    private function registrations_and_questions_and_answers()
    {
        $total_question_sets = 30;
        $minimum_questions = 5;
        $maximum_questions = 10;
        $maximum_choices = 6;
        $maximum_checkboxes = 6;
        $faker = Faker\Factory::create();
        $campers = User::campers()->get();
        // First, fake registrations of campers who are eligible for
        $this->log_seed('registrations');
        foreach (User::campers()->cursor() as $camper) {
            foreach (Camp::all() as $camp) {
                if (Common::randomVeryRareHit())
                    continue;
                try {
                    $camper->isEligibleForCamp($camp);
                } catch (\Exception $e) {
                    continue;
                }
                if (Registration::where('camp_id', $camp->id)->where('camper_id', $camper->id)->exists())
                    continue;
                $registration = Registration::create([
                    'camp_id' => $camp->id,
                    'camper_id' => $camper->id,
                    'submission_time' => now(),
                ]);
                // Randomly submit the application forms
                if (Common::randomFrequentHit()) {
                    $registration->status = RegistrationStatus::APPLIED;
                    $registration->save();
                }
                // Camps with registrations must obviously be approved first
                if (!$camp->approved) {
                    $camp->approved = true;
                    $camp->save();
                }
            }
        }
        // Fake questions of several types for the camps that require
        // ISSUE: This is < 100% effectiveness (Actual created question set records can be lowered than the total number)
        $this->log_seed('questions and answers');
        while ($total_question_sets--) {
            $json = [];
            $camp = Camp::inRandomOrder()->first();
            $camp_id = $camp->id;
            if (!$camp->camp_procedure()->candidate_required || QuestionSet::where('camp_id', $camp_id)->exists())
                continue;
            $question_set = QuestionSet::create([
                'camp_id' => $camp_id,
                'score_threshold' => rand(0, 75) / 100.0,
            ]);
            $json['camp_id'] = $camp_id;
            $json['type'] = [];
            $json['question'] = [];
            $json['question_required'] = [];
            $json['question_graded'] = [];
            $json['radio'] = [];
            $json['radio_label'] = [];
            $json['checkbox_label'] = [];
            $questions_number = rand($minimum_questions, $maximum_questions);
            while ($questions_number--) {
                $question_type = QuestionType::any();
                // Requirement: file upload is always required and graded
                $graded = $question_type == QuestionType::FILE ? true : rand(0, 1);
                $required = $graded ? true : rand(0, 1);
                $json_id = $this->randomID($camp_id);
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

                        break;
                    case QuestionType::PARAGRAPH:

                        break;
                    case QuestionType::CHOICES:
                        $choices_number = rand($maximum_choices / 2, $maximum_choices);
                        $json['radio_label'][$json_id] = [];
                        for ($i = 1; $i <= $choices_number; $i++) {
                            $choice_id = $this->randomID($camp_id);
                            $json['radio_label'][$json_id][$choice_id] = $faker->text($maxNbChars = 40);
                        }
                        $multiple_radio_map[$json_id] = $json['radio_label'][$json_id];
                        $correct_choice = array_rand($json['radio_label'][$json_id]);
                        $json['radio'][$json_id] = $correct_choice;
                        break;
                    case QuestionType::CHECKBOXES:
                        $checkboxes_number = rand($maximum_checkboxes / 2, $maximum_checkboxes);
                        $json['checkbox_label'][$json_id] = [];
                        for ($i = 1; $i <= $checkboxes_number; $i++) {
                            $checkbox_id = $this->randomID($camp_id);
                            $json['checkbox_label'][$json_id][$checkbox_id] = $faker->text($maxNbChars = 40);
                        }
                        $multiple_checkbox_map[$json_id] = $json['checkbox_label'][$json_id];
                        break;
                }
                $question = Question::create([
                    'json_id' => $json_id,
                    'type' => $question_type,
                    'full_score' => $graded ? 10.0 : null,
                ]);
                $pair = QuestionSetQuestionPair::create([
                    'question_set_id' => $question_set->id,
                    'question_id' => $question->id,
                ]);
                // For each question, all campers who are eligible and registered get a chance to answer
                foreach ($campers as $camper) {
                    try {
                        $camper->isEligibleForCamp($camp);
                    } catch (\Exception $e) {
                        continue;
                    }
                    $registration = $camp->getLatestRegistration($camper->id);
                    if (is_null($registration))
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
                            $answer = array_rand($multiple_radio_map[$json_id]);
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
                    $answer = Common::encodeIfNeeded($answer, $question_type);
                    Answer::create([
                        'question_set_id' => $question_set->id,
                        'question_id' => $question->id,
                        'camper_id' => $camper->id,
                        'registration_id' => $registration->id,
                        'answer' => $answer,
                    ]);
                }
                unset($multiple_radio_map);
                unset($multiple_checkbox_map);
            }
            // Empty fields are ruled out the same way the form POST does
            foreach ($json as $key => $value) {
                if (empty($value))
                    unset($json[$key]);
            }
            $json = json_encode($json);
            $directory = Common::questionSetDirectory($camp_id);
            Storage::disk('local')->put($directory.'/questions.json', $json);
        }
    }

    private function alter_campers()
    {
        $this->log_alter('campers');
        foreach (User::campers()->cursor() as $camper) {
            $camper->education_level = EducationLevel::any();
            $camper->blood_group = rand(0, 3);
            $camper->cgpa = rand(200, 400) / 100.0; // Assume campers are not that incompetent
            $camper->school_id = School::inRandomOrder()->first()->id;
            $camper->program_id = Program::inRandomOrder()->first()->id;
            $camper->save();
        }
        $candidate = User::_campers(true)->limit(1)->first();
        $candidate->username = 'camper';
        $candidate->status = 1;
        $candidate->activation_code = null;
        $candidate->cgpa = 3.6; // The candidate will be used to test certain camps so the smartening is needed
        $candidate->save();
    }

    private function alter_campmakers()
    {
        $this->log_alter('campmakers');
        foreach (User::campMakers()->cursor() as $campmaker) {
            $campmaker->organization_id = Organization::inRandomOrder()->first()->id;
            $campmaker->save();
        }
        $candidate = User::_campMakers(true)->limit(1)->first();
        $candidate->username = 'campmaker';
        $candidate->status = 1;
        $candidate->activation_code = null;
        $candidate->save();
    }

    private function create_admin()
    {
        $this->log_alter('admin');
        $admin = User::_campMakers(true)->limit(1)->first();
        $admin->type = config('const.account.admin');
        $admin->username = 'admin';
        $admin->name_en = 'Administrator';
        $admin->surname_en = '001';
        $admin->nickname_en = 'Admin';
        $admin->organization_id = null;
        $admin->status = 1;
        $admin->activation_code = null;
        $admin->save();
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->religions();
        $this->regions();
        $this->years();
        $this->programs();
        $this->badge_categories();
        $this->camp_categories();
        $this->camp_procedures();
        factory(School::class, 10)->create();
        factory(Organization::class, 10)->create();
        factory(Camp::class, 50)->create();
        factory(User::class, 70)->create();
        $this->alter_campers();
        $this->alter_campmakers();
        $this->create_admin();
        $this->registrations_and_questions_and_answers();
        $this->call([
            PermissionTableSeeder::class
        ]);
        Model::reguard();
    }
}
