<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_en', 50)->nullable();
            $table->string('name_th', 50)->nullable();
            $table->string('surname_en', 50)->nullable();
            $table->string('surname_th', 50)->nullable();
            $table->string('nickname_en', 50)->nullable();
            $table->string('nickname_th', 50)->nullable();
            $table->tinyInteger('nationality')->unsigned();
            $table->string('citizen_id');
            $table->tinyInteger('gender')->unsigned();
            $table->date('dob');
            $table->string('address', 300);
            $table->string('zipcode');
            $table->string('mobile_no')->nullable();
            $table->string('allergy', 200)->nullable();
            $table->string('email', 100)->unique();
            $table->string('username')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('activation_code')->nullable();
            $table->boolean('status')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->tinyInteger('type');
            $table->integer('religion_id')->unsigned();
            $table->foreign('religion_id')->references('id')->on('religions');

            // camp maker
            $table->integer('organization_id')->unsigned()->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations');

            // camper
            $table->double('cgpa', 3, 2)->nullable();
            $table->tinyInteger('mattayom')->nullable();
            $table->tinyInteger('blood_group')->nullable();
            $table->string('guardian_name')->nullable();
            $table->tinyInteger('guardian_role')->nullable();
            $table->string('guardian_mobile_no')->nullable();
            $table->integer('school_id')->unsigned()->nullable();
            $table->foreign('school_id')->references('id')->on('schools');
            $table->integer('program_id')->unsigned()->nullable();
            $table->foreign('program_id')->references('id')->on('programs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
