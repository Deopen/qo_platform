<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questioners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longText('description');
            $table->date("DateTime")->nullable();
            $table->time("time")->nullable();
            $table->boolean("required_all_questions")->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->string('accessibility')->default("private");//public , private , shared , test
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
        Schema::drop('questioners');
    }
}
