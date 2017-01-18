<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date');
            $table->string('location');
            $table->char('viewers', 1);
            $table->integer('team1_id');
            $table->integer('team2_id');
            $table->integer('referee1_id');
            $table->integer('referee2_id');
            $table->integer('referee3_id');
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
        //
    }
}
