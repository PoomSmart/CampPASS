<?php

use Illuminate\Database\Seeder;
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
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        factory(App\User::class, 10)->create();
        $this->programs();
        Model::reguard();
    }
}
