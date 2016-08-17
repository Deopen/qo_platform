<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('questioner_id')->unsigned();
            $table->string("class");
            $table->integer('begin')->unsigned();
            $table->integer('end')->unsigned();
            $table->text("value");
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
        Schema::drop('score_maps');
    }
}
