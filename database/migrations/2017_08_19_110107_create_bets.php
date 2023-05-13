<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('game_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('schedule_id');
            $table->unsignedInteger('team_id')->nullable();
            $table->smallInteger('type');
            $table->tinyInteger('status')->default(\App\Models\Bet::IN_GAME);
            $table->timestamps();
        });

        Schema::table('bets', function(Blueprint $table) {
            $table->foreign('game_id')->references('id')->on('games')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bets', function (Blueprint $table) {
            $table->dropForeign('bets_game_id_foreign');
            $table->dropForeign('bets_user_id_foreign');
            $table->dropForeign('bets_schedule_id_foreign');
        });
        Schema::dropIfExists('bets');
    }
}
