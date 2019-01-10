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
            $table->string('nameen')->nullable();
            $table->string('nameth')->nullable();
            $table->string('surnameen')->nullable();
            $table->string('surnameth')->nullable();
            $table->string('nicknameen')->nullable();
            $table->string('nicknameth')->nullable();
            $table->tinyInteger('nationality');
            $table->string('citizenid');
            $table->tinyInteger('gender');
            $table->date('dob');
            $table->string('address');
            $table->string('zipcode');
            $table->string('mobileno');
            $table->string('allergy')->nullable();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('activation_code')->nullable();
            $table->boolean('status')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->tinyInteger('type');

            // camper
            $table->string('shortbiography')->nullable();
            $table->tinyInteger('mattayom')->nullable();
            $table->tinyInteger('bloodgroup')->nullable();
            $table->string('guardianname')->nullable();
            $table->tinyInteger('guardianrole')->nullable();
            $table->string('guardianmobileno')->nullable();
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
