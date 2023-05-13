<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartGameTimeToSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unsignedInteger('start_game_time')->default(0);
        });

        $collection = \App\Models\Schedule::query()->get();
        foreach ($collection as $model) {
            $model->start_game_time = $model->date;
            $model->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('start_game_time');
        });
    }
}
