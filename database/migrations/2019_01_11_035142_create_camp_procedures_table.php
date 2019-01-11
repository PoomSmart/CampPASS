<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('camp_procedures', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
            $table->string('description');
            $table->boolean('interview_required')->default(false);
            $table->boolean('deposit_required')->default(false);
            $table->boolean('candidate_required')->default(false);
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
        Schema::dropIfExists('camp_procedures');
    }
}
