<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_sets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('camp_id')->unsigned();
            $table->foreign('camp_id')->references('id')->on('camps')->onDelete('cascade');
            $table->double('score_threshold')->nullable();
            $table->double('total_score')->nullable();
            $table->boolean('manual_required')->default(false);
            $table->boolean('auto_ranked')->default(false);
            $table->boolean('finalized')->default(false);
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
        Schema::dropIfExists('question_sets');
    }
}
