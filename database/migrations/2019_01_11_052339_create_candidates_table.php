<?php

use App\Enums\CandidateStatus;

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
            $table->integer('reg_id')->unsigned();
            $table->foreign('reg_id')->references('id')->on('registrations');
            $table->smallInteger('total_score')->default(0);
            $table->tinyInteger('status')->default(CandidateStatus::CHOSEN);
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
