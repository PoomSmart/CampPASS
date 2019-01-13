<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    private function programs()
    {
        DB::table('programs')->insert([
            [ 'name_en' => 'Math-Science', 'name_th' => 'วิทย์-คณิต' ],
            [ 'name_en' => 'Art', 'name_th' => 'ศิลป์-คำนวณ' ],
        ]);
    }

    private function religions()
    {
        DB::table('religions')->insert([
            [ 'name' => 'Buddhist' ],
            [ 'name' => 'Christ' ],
            [ 'name' => 'Islamic' ],
        ]);
    }

    private function alterCampers()
    {
        DB::table('users')->where('type', config('const.account.camper'))->update([
            'mattayom' => rand(1, 6),
            'blood_group' => rand(0, 3),
            'school_id' => DB::table('schools')->inRandomOrder()->pluck('id')->first(),
            'program_id' => DB::table('programs')->inRandomOrder()->pluck('id')->first(),
        ]);
    }

    private function alterCampMakers()
    {
        DB::table('users')->where('type', config('const.account.campmaker'))->update([
            'org_id' => DB::table('organizations')->inRandomOrder()->pluck('id')->first(),
        ]);
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
        factory(App\School::class, 5)->create();
        factory(App\User::class, 50)->create();
        $this->alterCampers();
        $this->alterCampMakers();
        Model::reguard();
    }
}
