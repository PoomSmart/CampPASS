<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('camp_id')->references('id')->on('camps');
            $table->foreign('camper_id')->references('id')->on('campers');
            $table->foreign('approved_by')->references('id')->on('campmakers');
            $table->enum('status', ['draft', 'applied', 'returned', 'approved', 'rejected'])->default('draft');
            $table->timestamps('submission_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registrations');
    }
}
