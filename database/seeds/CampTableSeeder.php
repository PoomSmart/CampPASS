<?php

use JeroenZwart\CsvSeeder\CsvSeeder;

use Illuminate\Support\Facades\DB;

class CampTableSeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->file = '/database/seeds/csvs/camps.csv';
        $this->tablename = 'camps';
        $this->type = [
            'name_en', 'name_th', 'camp_category_id', 'camp_procedure_id', 'organization_id',
            'acceptable_regions', 'acceptable_programs', 'short_description_en', 'short_description_th',
            'long_description_en', 'long_description_th', 'min_cgpa', 'other_conditions', 'application_fee',
            'deposit', 'url', 'fburl', 'app_close_date', 'confirmation_date', 'announcement_date',
            'interview_date', 'interview_information', 'event_start_date', 'event_end_date', 'quota',
            'backup_limit', 'contact_campmaker', 'payment_information',
        ];
        $this->validate = [
            'acceptable_programs' => 'required|json',
            'acceptable_regions' => 'required|json',
        ];
        $this->defaults = [
            'approved' => 1,
        ];
        $this->timestamps = false;
        $this->delimiter = ',';
    }

    public function run()
    {
        DB::disableQueryLog();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        parent::run();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}