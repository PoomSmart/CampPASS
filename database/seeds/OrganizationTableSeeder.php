<?php

use App\Enums\OrganizationType;

use JeroenZwart\CsvSeeder\CsvSeeder;

use Illuminate\Support\Facades\DB;

class OrganizationTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->file = '/database/seeds/csvs/universities.csv';
        $this->tablename = 'organizations';
        $this->type = [ 'name_th', 'subtype', 'image', ];
        $this->delimiter = ',';
        $this->defaults = [
            'type' => OrganizationType::UNIVERSITY,
        ];
    }
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::disableQueryLog();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        parent::run();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}