<?php

use App\Camp;
use App\CampCategory;
use App\CampProcedure;
use App\User;
use App\Program;
use App\Registration;
use App\Religion;
use App\School;
use App\Organization;

use Spatie\Permission\Models\Role;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    private function programs()
    {
        Program::insert([
            [ 'name' => 'Science' ],
            [ 'name' => 'Art' ],
        ]);
    }

    private function campCategories()
    {
        CampCategory::insert([
            [ 'name' => 'Engineering' ],
            [ 'name' => 'Architectural' ],
            [ 'name' => 'Economic' ],
            [ 'name' => 'Political' ],
            [ 'name' => 'Artistic' ],
            [ 'name' => 'Musical' ],
            [ 'name' => 'Pilot' ],
            [ 'name' => 'Argicultural' ],
        ]);
    }

    private function campProcedures()
    {
        // dummy TODO: make them real
        CampProcedure::insert([
            [ 'title' => 'Walk-in', 'description' => 'Walk-in', 'interview_required' => false, 'deposit_required' => false, 'candidate_required' => false ],
            [ 'title' => 'QA Only', 'description' => 'QA Only', 'interview_required' => false, 'deposit_required' => true, 'candidate_required' => false ],
            [ 'title' => 'Interview Only', 'description' => 'Interview Only', 'interview_required' => true, 'deposit_required' => false, 'candidate_required' => false ],
            [ 'title' => 'Pass QA then Interview', 'description' => 'Pass QA then deposit', 'interview_required' => true, 'deposit_required' => true, 'candidate_required' => false ],
            [ 'title' => 'Deposit Only', 'description' => 'Deposit Only', 'interview_required' => false, 'deposit_required' => false, 'candidate_required' => true ],
            [ 'title' => 'Pass QA then deposit', 'description' => 'Pass QA then deposit', 'interview_required' => true, 'deposit_required' => true, 'candidate_required' => false ],
            [ 'title' => 'Interview then Deposit', 'description' => 'Interview then Deposit', 'interview_required' => true, 'deposit_required' => false, 'candidate_required' => true ],
            [ 'title' => 'Pass QA then interview then deposit', 'description' => 'Pass QA then interview then deposit', 'interview_required' => true, 'deposit_required' => true, 'candidate_required' => true ],
        ]);
    }

    private function religions()
    {
        Religion::insert([
            [ 'name' => 'Buddhist' ],
            [ 'name' => 'Christ' ],
            [ 'name' => 'Islamic' ],
            [ 'name' => 'Other' ],
        ]);
    }

    private function alterCampers()
    {
        foreach (User::campers()->cursor() as $camper) {
            $camper->mattayom = rand(1, 6);
            $camper->blood_group = rand(0, 3);
            $camper->school_id = School::inRandomOrder()->first()->id;
            $camper->program_id = Program::inRandomOrder()->first()->id;
            $camper->save();
        }
        $candidate = User::_campers(true)->limit(1)->first();
        $candidate->username = 'camper';
        $candidate->status = 1;
        $candidate->activation_code = null;
        $candidate->save();
    }

    private function alterCampMakers()
    {
        foreach (User::campMakers()->cursor() as $campmaker) {
            $campmaker->org_id = Organization::inRandomOrder()->first()->id;
            $campmaker->save();
        }
        $candidate = User::campMakers()->whereIn('org_id', Camp::distinct('org_id')->select('org_id')->groupBy('org_id')->get())->limit(1)->first();
        $candidate->username = 'campmaker';
        $candidate->status = 1;
        $candidate->activation_code = null;
        $candidate->save();
    }

    private function createAdmin()
    {
        $admin = User::_campMakers(true)->limit(1)->first();
        $admin->type = config('const.account.admin');
        $admin->username = 'admin';
        $admin->name_en = 'Administrator';
        $admin->surname_en = '001';
        $admin->nickname_en = 'Admin';
        $admin->org_id = null;
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
        $this->programs();
        $this->campCategories();
        $this->campProcedures();
        factory(School::class, 5)->create();
        factory(Organization::class, 5)->create();
        factory(Camp::class, 10)->create();
        factory(User::class, 50)->create();
        $this->alterCampers();
        $this->alterCampMakers();
        $this->createAdmin();
        factory(Registration::class, 50)->create();
        $this->call([
            PermissionTableSeeder::class
        ]);
        Model::reguard();
    }
}
