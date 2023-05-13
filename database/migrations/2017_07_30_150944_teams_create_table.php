<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TeamsCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('cover')->default('');
            $table->integer('played_games');
            $table->integer('position');
            $table->integer('points');
            $table->integer('wins');
            $table->integer('draws');
            $table->integer('losses');
            $table->unsignedInteger('league_id');
            $table->timestamps();
        });

        Schema::table('teams', function(Blueprint $table) {
            $table->foreign('league_id')->references('id')->on('leagues')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign('teams_league_id_foreign');
        });
        Schema::dropIfExists('teams');
    }
}
