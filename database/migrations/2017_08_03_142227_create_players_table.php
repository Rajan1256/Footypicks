<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id');
            $table->string('name');
            $table->string('nationality');
            $table->string('position')->default('');
            $table->integer('jersey_number');
            $table->date('date_birth');
            $table->string('market_value')->default('');
            $table->string('contract_until')->default('');
        });

        Schema::table('players', function(Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign('players_team_id_foreign');
        });
        Schema::dropIfExists('players');
    }
}
