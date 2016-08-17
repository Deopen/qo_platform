<?php

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
            $table->string('name');
            $table->string('family');
            $table->string('username')->unique();
            $table->string('email')->nullable();
            $table->string('gender')->nullable();
            $table->Integer('age')->nullable();
            $table->string('password');
            $table->string('default_password');
            $table->string('access_level')->default("subject");
            $table->string('phone')->nullable();
            $table->longText('comments')->nullable();
            $table->Integer('project_limit')->unsigned();
            $table->Integer('questioner_limit')->unsigned();
            $table->Integer('created_by')->unsigned()->nullable();
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
        Schema::drop('users');
    }
}
