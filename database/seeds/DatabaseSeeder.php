<?php

use App\CampCategory;
use App\User;
use App\Program;
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
            [ 'name_en' => 'Science', 'name_th' => 'วิทย์' ],
            [ 'name_en' => 'Art', 'name_th' => 'ศิลป์' ],
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
        foreach (User::where('type', config('const.account.camper'))->cursor() as $camper) {
            $camper->mattayom = rand(1, 6);
            $camper->blood_group = rand(0, 3);
            $camper->school_id = School::inRandomOrder()->first()->id;
            $camper->program_id = Program::inRandomOrder()->first()->id;
            $camper->save();
        }
        $candidate = User::inRandomOrder()->where('type', config('const.account.camper'))->limit(1)->first();
        $candidate->username = 'camper';
        $candidate->status = 1;
        $candidate->activation_code = null;
        $candidate->save();
    }

    private function alterCampMakers()
    {
        foreach (User::where('type', config('const.account.campmaker'))->cursor() as $campmaker) {
            $campmaker->org_id = Organization::inRandomOrder()->first()->id;
            $campmaker->save();
        }
        $candidate = User::inRandomOrder()->where('type', config('const.account.campmaker'))->limit(1)->first();
        $candidate->username = 'campmaker';
        $candidate->status = 1;
        $candidate->activation_code = null;
        $candidate->save();
    }

    private function createAdmin()
    {
        $admin = User::inRandomOrder()->where('type', config('const.account.campmaker'))->limit(1)->first();
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
        factory(School::class, 5)->create();
        factory(Organization::class, 5)->create();
        factory(User::class, 50)->create();
        $this->alterCampers();
        $this->alterCampMakers();
        $this->createAdmin();
        $this->call([
            PermissionTableSeeder::class
        ]);
        Model::reguard();
    }
}
