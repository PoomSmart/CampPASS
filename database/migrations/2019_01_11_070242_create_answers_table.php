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
            $table->integer('queset_id')->unsigned();
            $table->foreign('queset_id')->references('id')->on('question_sets')->onDelete('cascade');
            $table->integer('que_id')->unsigned();
            $table->foreign('que_id')->references('id')->on('questions')->onDelete('cascade');
            $table->integer('camper_id')->unsigned();
            $table->foreign('camper_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('reg_id')->unsigned();
            $table->foreign('reg_id')->references('id')->on('registrations');
            $table->string('answer');
            $table->double('score')->default(0.0);
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
