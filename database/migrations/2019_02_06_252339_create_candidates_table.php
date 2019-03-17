<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('camper_id')->unsigned();
            $table->foreign('camper_id')->references('id')->on('users');
            $table->integer('camp_id')->unsigned();
            $table->foreign('camp_id')->references('id')->on('camps');
            $table->integer('registration_id')->unsigned();
            $table->foreign('registration_id')->references('id')->on('registrations');
            $table->integer('form_score_id')->unsigned();
            $table->foreign('form_score_id')->references('id')->on('form_scores');
            $table->smallInteger('total_score')->nullable();
            $table->boolean('backup')->default(false); // Whether the camper is a backup
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
        Schema::dropIfExists('candidates');
    }
}
