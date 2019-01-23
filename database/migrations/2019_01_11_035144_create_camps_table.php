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
        Storage::disk('local')->deleteDirectory('camps/'); // TODO: Decide whether this should exist in the future
        Schema::create('camps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('camp_category_id')->unsigned();
            $table->foreign('camp_category_id')->references('id')->on('camp_categories');
            $table->integer('organization_id')->unsigned();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->integer('camp_procedure_id')->unsigned();
            $table->foreign('camp_procedure_id')->references('id')->on('camp_procedures');
            $table->json('acceptable_regions');
            $table->string('name_en', 100)->nullable();
            $table->string('name_th', 100)->nullable();
            $table->string('short_description_en', 200)->nullable();
            $table->string('short_description_th', 200)->nullable();
            $table->json('acceptable_programs')->nullable();
            $table->double('min_gpa', 3, 2)->nullable();
            $table->string('other_conditions', 200)->nullable();
            $table->integer('application_fee')->unsigned()->nullable(); // some camps don't cost campers
            $table->string('url', 150)->nullable();
            $table->string('fburl', 150)->nullable();
            $table->date('app_opendate')->nullable(); // some camps don't require application
            $table->date('app_closedate')->nullable(); // same as above
            $table->date('reg_opendate')->nullable(); // registration date may depend on applications
            $table->date('reg_closedate')->nullable(); // registration deadline may be undecided
            $table->date('event_startdate')->nullable(); // event start date may be undecided
            $table->date('event_enddate')->nullable(); // same as above
            $table->double('event_location_lat')->nullable(); // event place may be undecided
            $table->double('event_location_long')->nullable(); // same as above
            $table->smallInteger('quota')->unsigned()->nullable();
            $table->boolean('approved')->default(false); // every camp needs approval
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
