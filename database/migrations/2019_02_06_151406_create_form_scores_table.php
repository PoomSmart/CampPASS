<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('registration_id')->unsigned();
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->integer('question_set_id')->unsigned();
            $table->foreign('question_set_id')->references('id')->on('question_sets')->onDelete('cascade');
            $table->double('total_score')->nullable();
            $table->boolean('finalized')->default(false); // Whether the grading is done
            $table->boolean('checked')->default(false); // Whether the document checking is done
            $table->boolean('passed')->default(false); // Whether the camper is passed
            $table->timestamp('submission_time')->nullable();
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
        Schema::dropIfExists('form_scores');
    }
}
