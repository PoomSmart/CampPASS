<?php

use JeroenZwart\CsvSeeder\CsvSeeder;

use Illuminate\Support\Facades\DB;

class SchoolTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->file = '/database/seeds/csvs/schools.csv';
        $this->type = [ 'name_th', ];
        $this->timestamps = false;
        $this->delimiter = ',';
        $this->table = 'schools';
    }
    
    public function run()
    {
	    DB::disableQueryLog();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        parent::run();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}