<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionSetQuestionPairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_set_question_pairs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('queset_id')->unsigned();
            $table->foreign('queset_id')->references('id')->on('question_sets')->onDelete('cascade');
            $table->integer('que_id')->unsigned();
            $table->foreign('que_id')->references('id')->on('questions')->onDelete('cascade');
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
        Schema::dropIfExists('question_set_question_pairs');
    }
}
