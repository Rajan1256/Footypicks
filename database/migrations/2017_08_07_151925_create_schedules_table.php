<?php
use App\Models\League;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('league_id');
            $table->unsignedInteger('team_home_id');
            $table->unsignedInteger('team_home_id_parse');
            $table->unsignedInteger('team_away_id');
            $table->unsignedInteger('team_away_id_parse');
            $table->string('date')->default('');
            $table->unsignedInteger('matchday')->default(0);
            $table->smallInteger('status')->default(League::ACTIVE);
            $table->tinyInteger('goals_home_team');
            $table->tinyInteger('goals_away_team');
        });

        Schema::table('schedules', function(Blueprint $table) {
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
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign('schedules_league_id_foreign');
        });
        Schema::dropIfExists('schedules');
    }
}
