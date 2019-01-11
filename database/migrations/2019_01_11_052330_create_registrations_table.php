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
            $table->integer('camp_id')->unsigned();
            $table->foreign('camp_id')->references('id')->on('camps');
            $table->integer('camper_id')->unsigned();
            $table->foreign('camper_id')->references('id')->on('users');
            $table->integer('approved_by')->unsigned();
            $table->foreign('approved_by')->references('id')->on('users');
            $table->enum('status', ['draft', 'applied', 'returned', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submission_time');
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
        Schema::dropIfExists('registrations');
    }
}
