<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_set_id')->unsigned();
            $table->foreign('question_set_id')->references('id')->on('question_sets')->onDelete('cascade');
            $table->integer('question_id')->unsigned();
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->integer('camper_id')->unsigned();
            $table->foreign('camper_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('registration_id')->unsigned();
            $table->foreign('registration_id')->references('id')->on('registrations');
            $table->text('answer')->nullable();
            $table->double('score')->nullable();
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
        Schema::dropIfExists('answers');
    }
}
