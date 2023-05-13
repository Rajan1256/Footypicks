<?php

use App\Models\Base;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStandings extends Migration
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
            $table->unsignedInteger('league_id');
            $table->unsignedInteger('user_id');
            $table->string('name');
            $table->tinyInteger('status')->default(Base::ACTIVE);
            $table->timestamps();
        });

        Schema::table('games', function(Blueprint $table) {
            $table->foreign('league_id')->references('id')->on('leagues')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('users_in_games', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('game_id');
            $table->unsignedInteger('user_id');
            $table->tinyInteger('status')->default(\App\Models\UserInGame::NOT_CONFIRM_STATUS);
        });


        Schema::table('users_in_games', function(Blueprint $table) {
            $table->foreign('game_id')->references('id')->on('games')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign('games_league_id_foreign');
            $table->dropForeign('games_user_id_foreign');
        });
        Schema::table('users_in_games', function (Blueprint $table) {
            $table->dropForeign('users_in_games_game_id_foreign');
            $table->dropForeign('users_in_games_user_id_foreign');
        });
        Schema::dropIfExists('users_in_games');
        Schema::dropIfExists('games');
    }
}
