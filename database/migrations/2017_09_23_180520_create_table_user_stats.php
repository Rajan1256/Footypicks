<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Services\HeadToHead as Service;
use App\Models\HeadToHead as Model;

class CreateTableUserStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->smallInteger('games_win')->default(0);
            $table->smallInteger('games_lose')->default(0);
            $table->smallInteger('hth_win')->default(0);
            $table->smallInteger('hth_lose')->default(0);
            $table->smallInteger('dares_win')->default(0);
            $table->smallInteger('dares_lose')->default(0);
        });

        Schema::table('user_stats', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        $usersCollection = \App\Models\User::query()->get();
        $usersCollection->map(function($user) {
            $model = new \App\Models\UserStat();
            $model->hth_win = Service::getWinHthGames($user->id, Model::GAME_TYPE_SINGLE);
            $model->hth_lose = Service::getLoseHthGames($user->id, Model::GAME_TYPE_SINGLE);
            $model->dares_win = Service::getWinHthGames($user->id, Model::GAME_TYPE_DARE);
            $model->dares_lose = Service::getLoseHthGames($user->id, Model::GAME_TYPE_DARE);
            $user->userStat()->save($model);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_stats', function (Blueprint $table) {
            $table->dropForeign('user_stats_user_id_foreign');
        });
        Schema::dropIfExists('user_stats');
    }
}
