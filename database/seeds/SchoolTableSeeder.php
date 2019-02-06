<?php

use JeroenZwart\CsvSeeder\CsvSeeder;

use DB;

class SchoolTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->file = '/database/seeds/csvs/schools.csv';
        $this->type = ['name_th'];
        $this->table = 'schools';
    }
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    DB::disableQueryLog();
	    parent::run();
    }
}