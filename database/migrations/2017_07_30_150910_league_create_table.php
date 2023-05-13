<?php

use App\Models\League;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LeagueCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leagues', function (Blueprint $table) {
            $table->increments('id');
            $table->string('caption');
            $table->string('name');
            $table->string('cover')->default('');
            $table->integer('teams_count');
            $table->integer('games_count');
            $table->integer('current_matchday');
            $table->integer('matchdays_count');
            $table->integer('league_parse_id');
            $table->string('last_updated');
            $table->smallInteger('status')->default(League::ACTIVE);
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
        Schema::dropIfExists('leagues');
    }
}
