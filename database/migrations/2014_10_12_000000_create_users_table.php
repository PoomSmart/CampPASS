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
            $table->string('nameen');
            $table->string('nameth')->nullable();
            $table->string('surnameen');
            $table->string('surnameth')->nullable();
            $table->string('nicknameen');
            $table->string('nicknameth')->nullable();
            $table->tinyInteger('nationality');
            $table->integer('citizenid');
            $table->tinyInteger('gender');
            $table->date('dob');
            $table->string('address');
            $table->int('zipcode');
            $table->string('mobileno');
            $table->string('allergy')->nullable();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('activation_code')->nullable();
            $table->boolean('status')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
