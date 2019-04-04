<?php

use JeroenZwart\CsvSeeder\CsvSeeder;

use Illuminate\Support\Facades\DB;

class CampTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->file = '/database/seeds/csvs/camps.csv';
        $this->tablename = 'camps';
        $this->type = [ 'name_en', 'name_th', ];
        $this->timestamps = false;
        $this->delimiter = ',';
        $this->defaults = [
            'xx' => 0,
        ];
    }

    public function run()
    {
        DB::disableQueryLog();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        parent::run();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}