<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Storage::deleteDirectory('camps/');
        Schema::create('camps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('camp_category_id')->unsigned();
            $table->foreign('camp_category_id')->references('id')->on('camp_categories');
            $table->integer('organization_id')->unsigned();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->integer('camp_procedure_id')->unsigned();
            $table->foreign('camp_procedure_id')->references('id')->on('camp_procedures');
            $table->json('acceptable_regions');
            $table->json('acceptable_programs');
            $table->json('acceptable_education_levels');
            $table->string('name_en', 100)->nullable();
            $table->string('name_th', 100)->nullable();
            $table->string('short_description_en', 400)->nullable();
            $table->string('short_description_th', 400)->nullable();
            $table->text('long_description')->nullable();
            $table->double('min_cgpa', 3, 2)->nullable();
            $table->string('other_conditions', 200)->nullable();
            $table->integer('application_fee')->unsigned()->nullable();
            $table->integer('deposit')->unsigned()->nullable();
            $table->string('url', 400)->nullable();
            $table->string('fburl', 400)->nullable();
            $table->dateTime('app_open_date');
            $table->dateTime('app_close_date');
            $table->dateTime('confirmation_date')->nullable();
            $table->dateTime('announcement_date')->nullable();
            $table->dateTime('interview_date')->nullable();
            $table->text('interview_information')->nullable();
            $table->dateTime('event_start_date');
            $table->dateTime('event_end_date');
            // $table->double('event_location_lat')->nullable(); // event place may be undecided
            // $table->double('event_location_long')->nullable(); // same as above
            $table->string('banner', 100)->default('banner.jpg');
            $table->string('poster', 100)->default('poster.jpg');
            $table->string('parental_consent', 100)->nullable();
            $table->smallInteger('quota')->unsigned()->nullable();
            $table->tinyInteger('backup_limit')->unsigned()->nullable();
            $table->text('contact_campmaker')->nullable();
            $table->string('payment_information')->nullable();
            $table->boolean('approved')->default(false); // every camp needs approval
            $table->boolean('candidate_announced')->default(false);
            $table->boolean('interview_announced')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('camps');
    }
}
