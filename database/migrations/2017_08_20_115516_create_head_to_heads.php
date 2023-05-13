<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHeadToHeads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('head_to_heads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('schedule_id');
            $table->unsignedInteger('win_user_id')->nullable();
            $table->string('wish')->default('');
            $table->tinyInteger('status')->default(\App\Models\HeadToHead::ACTIVE);
            $table->timestamps();
        });

        Schema::table('head_to_heads', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('win_user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('head_to_head_bets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('head_to_head_id');
            $table->unsignedTinyInteger('goals_home_team')->default(0);
            $table->unsignedTinyInteger('goals_away_team')->default(0);
            $table->unsignedInteger('team_id')->nullable();
            $table->smallInteger('type');
            $table->timestamps();
        });

        Schema::table('head_to_head_bets', function(Blueprint $table) {
            $table->foreign('head_to_head_id')->references('id')->on('head_to_heads')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('head_to_head_invites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('head_to_head_id');
            $table->tinyInteger('status')->default(\App\Models\HeadToHeadInvite::STATUS_INVITED);
            $table->timestamps();
        });

        Schema::table('head_to_head_invites', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('head_to_head_id')->references('id')->on('head_to_heads')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('head_to_head_invites', function (Blueprint $table) {
            $table->dropForeign('head_to_head_invites_head_to_head_id_foreign');
            $table->dropForeign('head_to_head_invites_user_id_foreign');
        });
        Schema::table('head_to_head_bets', function (Blueprint $table) {
            $table->dropForeign('head_to_head_bets_head_to_head_id_foreign');
            $table->dropForeign('head_to_head_bets_user_id_foreign');
            $table->dropForeign('head_to_head_bets_team_id_foreign');
        });

        Schema::table('head_to_heads', function (Blueprint $table) {
            $table->dropForeign('head_to_heads_schedule_id_foreign');
            $table->dropForeign('head_to_heads_user_id_foreign');
            $table->dropForeign('head_to_heads_win_user_id_foreign');
        });
        Schema::dropIfExists('head_to_head_invites');
        Schema::dropIfExists('head_to_head_bets');
        Schema::dropIfExists('head_to_heads');
    }
}
